<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: users/student.php");
    exit();
}

// Check if the form was submitted and triggered by the upload button
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_triggered'])) {
    // Database credentials
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "usgp";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve form data
    $stud_name = $_POST['stud_name'] ?? '';
    $course = $_POST['stud_course'] ?? '';
    $tree_species = $_POST['tree_species'] ?? '';
    $date_planted = $_POST['date'] ?? '';
    $latitude = $_POST['latitude'] ?? 0;
    $longitude = $_POST['longitude'] ?? 0;
    $submitted_date = date('Y-m-d H:i:s');

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO student_records (stud_name, stud_course, tree_species, date_planted, latitude, longitude, date_submitted) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssdds", $stud_name, $course, $tree_species, $date_planted, $latitude, $longitude, $submitted_date);

    // Execute the statement
    if ($stmt->execute()) {
        // Set a session variable to indicate success
        $_SESSION['upload_success'] = true;

        // Redirect to avoid resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/stud-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .location-info {
            margin-top: 10px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.html'; ?>

    <div id="main">
        <div class="header">
            <h5>Perlaoan, Jelmark M.</h5>
            <h6>Student</h6>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Semester <br>A/Y</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody id="tablaDatos">
                <tr>
                    <td>1</td>
                    <td>Perlaoan, Jelmark M.</td>
                    <td>2nd</td>
                    <td>Pending</td>
                    <td class="acciones"><button class="upload " data-toggle="modal" data-target="#modelId">Upload</button></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <div id="records">
            <p>Current Semester: <b>2nd semester</b></p>
            <p>No. of stay: <b>2</b></p>
            <p>Total response: <b>3</b></p>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Camera Upload</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="cl">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="upload_triggered" value="1">

                        <div class="input-content">
                            <div class="content">
                                <div class="add-dup-parent">
                                    <div class="form-row mt-3 __add-fields">
                                        <div class="col-9">
                                            <div class="card form-image-preview">
                                                <input type="file" name="file_image" accept="image/*" class="user-file" capture="camera">
                                                <div class="card-body upload-area upload-file" id="uploadfile">
                                                    <p class="card-text">
                                                        <i class="fa fa-camera fa-lg" aria-hidden="true"></i> <br>
                                                        Click to take a photo
                                                    </p>
                                                    <img class="card-img-top" src="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="location-info" id="locationInfo"></div>

                                <input type="hidden" id="latitude" name="latitude">
                                <input type="hidden" id="longitude" name="longitude">
                                <input type="text" name="stud_name" class="input" placeholder="Name" required>
                                <input type="text" name="stud_course" class="input" placeholder="Course" required>
                                <input type="text" name="tree_species" class="input" placeholder="Tree Species" required>
                                <label for="date">Date Planted</label><input type="date" name="date" class="input" required>
                                <br />
                                <button type="submit" class="btn btn-primary btn-lg shadow-lg border-0 rounded" id="saveButton">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        function img_preview(input, t) {
            if (t == 'single') {
                let preview_img = $(input).closest('.form-image-preview').find('img');
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        preview_img.attr('src', e.target.result);
                    };
                    reader.readAsDataURL(input.files[0]); // convert to base64 string
                }
                preview_img.fadeIn(); //display
            }
        }

        function delete_field(obj, elem) {
            $(obj).closest(elem).fadeOut().remove();
            return false;
        }

        let check_id = 0;

        function add_extra_field(obj) {
            check_id++;
            let parent = $(obj).data('parent');
            let clone = $(obj).data('clone');
            let html = $(clone).clone();
            $(parent).append(html.attr('id', '').fadeIn());
            html.find('.upload-file').attr('id', `uploadfile${check_id}`);
        }

        $('#modelId').on('shown.bs.modal', function() {
            requestLocationAccess();

            $(function() {
                $("html").on("dragover", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
                $("html").on("drop", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });

                $(".add-dup-parent").on('dragenter', '.upload-area', function(e) {
                    e.preventDefault();
                    $(this).find("p").css('background', '#BBD5B8')
                });

                $(".add-dup-parent").on('dragover', '.upload-area', function(e) {
                    e.preventDefault();
                    $(this).find("p").css('background', '#BBD5B8')
                });

                $(".add-dup-parent").on('dragleave', '.upload-area', function(e) {
                    e.preventDefault();
                    $(this).find("p").css('background', '#F8F8F8')
                });

                $(".add-dup-parent").on('drop', '.upload-area', function(e) {
                    e.preventDefault();
                    $(this).find("p").css('background', '#F8F8F8')
                    var files = e.originalEvent.dataTransfer.files;
                    $(this).find('input').prop('files', files);
                    img_preview($(this).find('input')[0], 'single');
                });

                $('#uploadForm').on('submit', function(e) {
                    e.preventDefault();
                    // Add form validation or submission logic here
                    console.log("Form submitted");
                });
            });
        });

        function requestLocationAccess() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.getElementById('latitude').value = position.coords.latitude;
                        document.getElementById('longitude').value = position.coords.longitude;
                        document.getElementById('locationInfo').innerText = `Latitude: ${position.coords.latitude}, Longitude: ${position.coords.longitude}`;
                    },
                    function(error) {
                        let message = '';
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                message = "User denied the request for Geolocation.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                message = "Location information is unavailable.";
                                break;
                            case error.TIMEOUT:
                                message = "The request to get user location timed out.";
                                break;
                            case error.UNKNOWN_ERROR:
                                message = "An unknown error occurred.";
                                break;
                        }
                        document.getElementById('locationInfo').innerText = message;
                    }
                );
            } else {
                document.getElementById('locationInfo').innerText = "Geolocation is not supported by this browser.";
            }
        }
    </script>
</body>
</html>
