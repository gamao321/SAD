<?php
session_start();
require_once 'db_config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$sql = "SELECT * FROM users ORDER BY surname ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Student Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
            cursor: pointer;
        }
        .search {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
        }
        select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 20px;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
        }
        .close {
            float: right;
            cursor: pointer;
            font-size: 24px;
        }
        .student-details {
            margin-top: 20px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        .detail-label {
            width: 150px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Student Records</h1>
            <a href="logout.php" style="text-decoration: none; color: red;">Logout</a>
        </div>
        
        <div class="filters">
            <input type="text" id="searchInput" class="search" placeholder="Search students...">
            <select id="courseFilter">
                <option value="">All Courses</option>
                <option value="BSIS">BSIS</option>
                <option value="DHRT">DHRT</option>
                <option value="ACT">ACT</option>
                <option value="DIT">DIT</option>
                <option value="STEM">STEM</option>
                <option value="GAS">GAS</option>
                <option value="ABM">ABM</option>
                <option value="TVL">TVL</option>
                <option value="ICT">ICT</option>
            </select>
            <select id="yearFilter">
                <option value="">All Years</option>
                <option value="1st Year">1st Year</option>
                <option value="2nd Year">2nd Year</option>
                <option value="3rd Year">3rd Year</option>
                <option value="4th Year">4th Year</option>
                <option value="Grade 11">Grade 11</option>
                <option value="Grade 12">Grade 12</option>
            </select>
        </div>

        <table id="studentsTable">
            <thead>
                <tr>
                    <th>LRN</th>
                    <th>Name</th>
                    <th>Course/Track</th>
                    <th>Year Level</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr onclick='showStudentDetails(\"".$row['lrn']."\")'>";
                        echo "<td>".$row['lrn']."</td>";
                        echo "<td>".$row['surname'].", ".$row['first_name']." ".$row['middle_name']."</td>";
                        echo "<td>".$row['course_track']."</td>";
                        echo "<td>".$row['year_level']."</td>";
                        echo "<td>".$row['email']."</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <div id="studentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Student Details</h2>
            <div id="studentDetails" class="student-details">
                <!-- Details will be populated here -->
            </div>
        </div>
    </div>

    <script>
        function showStudentDetails(lrn) {
            fetch('get_student_details.php?lrn=' + lrn)
                .then(response => response.json())
                .then(data => {
                    const detailsHtml = `
                        <div class="detail-row">
                            <div class="detail-label">LRN:</div>
                            <div>${data.lrn}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Full Name:</div>
                            <div>${data.surname}, ${data.first_name} ${data.middle_name}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Course/Track:</div>
                            <div>${data.course_track}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Year Level:</div>
                            <div>${data.year_level}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Email:</div>
                            <div>${data.email}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Address:</div>
                            <div>${data.address}</div>
                        </div>
                    `;
                    document.getElementById('studentDetails').innerHTML = detailsHtml;
                    document.getElementById('studentModal').style.display = 'block';
                });
        }

        function closeModal() {
            document.getElementById('studentModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('studentModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Search and filter functionality
        document.getElementById('searchInput').addEventListener('keyup', filterTable);
        document.getElementById('courseFilter').addEventListener('change', filterTable);
        document.getElementById('yearFilter').addEventListener('change', filterTable);

        function filterTable() {
            const searchText = document.getElementById('searchInput').value.toLowerCase();
            const courseFilter = document.getElementById('courseFilter').value;
            const yearFilter = document.getElementById('yearFilter').value;
            const table = document.getElementById('studentsTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                const rowText = Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ');
                const course = cells[2].textContent;
                const year = cells[3].textContent;

                const matchesSearch = rowText.includes(searchText);
                const matchesCourse = !courseFilter || course === courseFilter;
                const matchesYear = !yearFilter || year === yearFilter;

                row.style.display = matchesSearch && matchesCourse && matchesYear ? '' : 'none';
            }
        }
    </script>
</body>
</html>