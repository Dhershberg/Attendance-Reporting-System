<?php

include 'config.php'; // Adjust the path if necessary

function saveReport($reportType, $reportDate, $reportTime)
{
    global $conn;
    $stmt = $conn->prepare("SELECT count(*) as count_reports FROM attendance WHERE report_date = ? ");
    $stmt->bind_param("s", $reportDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc();
    $count = $count['count_reports'];

    if ($count == 0 && $reportType == 'in') {
        $stmt = $conn->prepare("INSERT INTO attendance (report_type, report_date, report_time) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $reportType, $reportDate, $reportTime);
        $stmt->execute();

        return json_encode(["success" => "Report saved successfully."]);
    }
    if ($count == 0 && $reportType == 'out') {
        return json_encode(["error" => "You have not yet reported entry for the requested day."]);
    }

    $stmt2 = $conn->prepare("SELECT * FROM attendance 
    WHERE report_date = ?
    ORDER BY report_time DESC 
    LIMIT 1");

    $stmt2->bind_param("s", $reportDate);
    $stmt2->execute();
    $ans = $stmt2->get_result();
    $row = $ans->fetch_assoc();



    if ($count > 0) {
        if ($reportType == 'in') {
            if ($count % 2 == 1) {
                return json_encode(["error" => "An exit report is missing for the requested day."]);
            } else {
                if ($row['report_time'] > $reportTime) {
                    return json_encode(["error" => "Time overlaps with existing reports."]);
                }
            }
        } else {
            if ($count % 2 == 0) {
                return json_encode(["error" => "An entry report is missing for the requested day."]);
            } else {
                if ($row['report_time'] > $reportTime) {
                    return json_encode(["error" => "Time overlaps with existing reports."]);
                }
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO attendance (report_type, report_date, report_time) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $reportType, $reportDate, $reportTime);
    $stmt->execute();

    return json_encode(["success" => "Report saved successfully."]);
}

function getReports()
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM attendance ORDER BY report_date DESC, report_time ASC LIMIT 20");
    $stmt->execute();
    $result = $stmt->get_result();

    $reports = [];
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
    return json_encode($reports);
}

function calculateMonthlyHours($month, $year)
{
    global $conn;

    $stmt = $conn->prepare("SELECT SUM(tempTable.minutes_difference) as total_hours from 
    (WITH tbl AS ( SELECT *, ROW_NUMBER() OVER (ORDER BY report_time) AS rn 
    FROM attendance WHERE MONTH(report_date) = $month AND YEAR(report_date) = $year) 
    SELECT enteryTable.report_date, 
    enteryTable.report_time AS enter_time, exitTable.report_time AS exit_time,
    TIMESTAMPDIFF(MINUTE, enteryTable.report_time, exitTable.report_time)
    AS minutes_difference FROM tbl AS enteryTable JOIN tbl AS exitTable 
    ON enteryTable.rn = exitTable.rn - 1 WHERE enteryTable.rn % 2 = 1)AS tempTable");

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total_hours'] / 60 ?? 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'calculate_hours') {
        $month = $_POST['month'];
        $year = $_POST['year'];
        $totalHours = calculateMonthlyHours($month, $year);
        echo $totalHours;
    } else {
        $reportType = $_POST['report_type'];
        $reportDate = $_POST['report_date'];
        $reportTime = $_POST['report_time'];
        echo saveReport($reportType, $reportDate, $reportTime);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo getReports();
}


$conn->close();
?>