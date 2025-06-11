<?php
session_start();
require_once 'db.php';

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #007BFF;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        footer {
            background-color: #007BFF;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 40px;
            font-size: 14px;
        }

        .container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px 40px;
            max-width: 600px;
            width: 100%;
            margin: 40px auto;
        }

        h2, h3 {
            text-align: center;
            color: #333;
        }

        #search-box {
            width: 100%;
            padding: 10px 14px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        #search-results {
            border: 1px solid #ccc;
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 200px;
            overflow-y: auto;
            background: #fff;
            position: relative;
            z-index: 10;
            display: none;
        }

        #search-results li {
            padding: 10px 14px;
            cursor: pointer;
        }

        #search-results li:hover {
            background-color: #f0f0f0;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        select, input[type="date"] {
            width: 95%;
            padding: 10px 14px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        button {
            background-color: #007BFF;
            color: white;
            font-size: 16px;
            padding: 12px;
            border: none;
            border-radius: 8px;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .success {
            color: green;
            font-weight: bold;
            text-align: center;
            margin-top: 15px;
        }

        .error {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-top: 15px;
        }

        #schedule-section {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<header>
    PulseScheduler - Book Appointment
</header>

<div class="container">
    <h2>Book an Appointment</h2>
    <a href="javascript:history.back()">Back</a>
    <input type="text" id="search-box" placeholder="Search doctor by name, BMDC, specialization, chamber name or address...">
    <ul id="search-results"></ul>

    <div id="schedule-section" style="display:none;">
        <h3 id="doctor-info"></h3>
        <form id="appointment-form">
            <input type="hidden" name="doctor_id" id="doctor_id">
            <input type="hidden" name="chamber_id" id="chamber_id">

            <label for="schedule_id">Available Schedules:</label>
            <select name="schedule_id" id="schedule_id" required></select>

            <label for="appointment_date">Choose Appointment Date:</label>
            <input type="date" name="appointment_date" id="appointment_date" required>

            <button type="submit">Book Appointment</button>
        </form>
        <div id="booking-message"></div>
    </div>
</div>
<br><br><br><br><br><br><br><br>
<footer>
    &copy; <?= date('Y') ?> PulseScheduler. All rights reserved.
</footer> 
  
<script>
document.getElementById("search-box").addEventListener("keyup", function () {
    const query = this.value.trim();
    if (query.length < 2) {
        document.getElementById("search-results").style.display = "none";
        return;
    }

    fetch("search_doctor.php?q=" + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            console.log("Doctors found:", data);
            const resultBox = document.getElementById("search-results");
            resultBox.innerHTML = "";
            data.forEach(doctor => {
                const li = document.createElement("li");
                const displayText = `${doctor.doctor_name} (${doctor.specialization}) - ${doctor.chamber_name}, ${doctor.chamber_address}`;
                li.textContent = displayText;
                li.addEventListener("click", function () {
                    console.log("Clicked doctor:", doctor);
                    document.getElementById("search-box").value = displayText;
                    resultBox.style.display = "none";
                    loadSchedule(doctor.doctor_id, doctor.chamber_id);
                });
                resultBox.appendChild(li);
            });
            resultBox.style.display = "block";
        })
        .catch(err => console.error("Error fetching doctors:", err));
});

// Function to convert 24-hour time to 12-hour AM/PM format
function to12HourFormat(time) {
    let [hours, minutes] = time.split(':');  // Split the time string into hours and minutes
    hours = parseInt(hours, 10);  // Convert hours to integer

    let period = hours >= 12 ? 'PM' : 'AM';  // Determine AM or PM
    hours = hours % 12;  // Convert 24-hour format to 12-hour format
    hours = hours ? hours : 12;  // Handle the case for 0 hours (12 AM)

    return `${hours}:${minutes} ${period}`;
}

function loadSchedule(doctorId, chamberId) {
    console.log("Loading schedule for:", doctorId, chamberId); // Debugging
    fetch(`load_schedule.php?doctor_id=${doctorId}&chamber_id=${chamberId}`)
        .then(response => {
            console.log("Raw Response Status:", response.status);
            return response.json(); 
        })
        .then(data => {
            console.log("Load Schedule Data: ", data);  // Check the full response for debugging
            const select = document.getElementById("schedule_id");
            select.innerHTML = ""; // Clear the existing options

            if (data.length === 0) {
                const option = document.createElement("option");
                option.disabled = true;
                option.textContent = "No available schedule";
                select.appendChild(option);
            } else {
                data.forEach(schedule => {
                    const option = document.createElement("option");
                    const startTimeAMPM = to12HourFormat(schedule.start_time);
                    const endTimeAMPM = to12HourFormat(schedule.end_time);
                    option.value = JSON.stringify(schedule);
                    option.textContent = `${schedule.day} - ${startTimeAMPM} to ${endTimeAMPM} - Fee: ${schedule.consultation_fee}`;
                    select.appendChild(option);
                });
            }

            document.getElementById("doctor_id").value = doctorId;
            document.getElementById("chamber_id").value = chamberId;
            document.getElementById("schedule-section").style.display = "block";
        })
        .catch(err => {
            console.error("Error loading schedule:", err);
            const select = document.getElementById("schedule_id");
            select.innerHTML = ""; // Clear the options in case of an error
            const option = document.createElement("option");
            option.disabled = true;
            option.textContent = "Error loading schedule";
            select.appendChild(option);
        });
}


document.getElementById("appointment-form").addEventListener("submit", function (e) {
    e.preventDefault();

    const doctorId = document.getElementById("doctor_id").value;
    const chamberId = document.getElementById("chamber_id").value;
    const appointmentDate = document.getElementById("appointment_date").value;
    const selectedSchedule = document.getElementById("schedule_id").value;

    const formData = new FormData();
    formData.append("doctor_id", doctorId);
    formData.append("chamber_id", chamberId);
    formData.append("appointment_date", appointmentDate);
    formData.append("slot", selectedSchedule); // slot is the JSON stringified schedule
    console.log("Slot Value:", formData.get("slot"));
    fetch("confirm_appointment.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        const msgDiv = document.getElementById("booking-message");
        if (data.success) {
            msgDiv.innerHTML = `<p class='success'>✅ Appointment booked successfully. Your serial is ${data.serial_no}</p>`;
        } else {
            msgDiv.innerHTML = `<p class='error'>❌ ${data.message}</p>`;
        }
    })
    .catch(err => {
        document.getElementById("booking-message").innerHTML = `<p class='error'>❌ Something went wrong. Please try again.</p>`;
        console.error("Booking error:", err);
    });
});
</script>

</body>
</html>
