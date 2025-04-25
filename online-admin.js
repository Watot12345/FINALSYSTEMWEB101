document.addEventListener("DOMContentLoaded", function () {
  // Function to toggle the online/offline status for each dot
  function changeStatus(dotId, textId) {
    const statusDot = document.getElementById(dotId);
    const statusText = document.getElementById(textId);

    if (statusDot.classList.contains("offline")) {
      // Change to online
      statusDot.classList.remove("offline");
      statusDot.classList.add("online");
      statusText.textContent = "Online";
      statusText.classList.remove("status-text-offline");
      statusText.classList.add("status-text-online");
    } else {
      // Change to offline
      statusDot.classList.remove("online");
      statusDot.classList.add("offline");
      statusText.textContent = "Offline";
      statusText.classList.remove("status-text-online");
      statusText.classList.add("status-text-offline");
    }
  }

  // Function to start the toggling with delays for each status dot
  function initStatusDots() {
    const delays = [0, 5000, 2000, 15000]; // Delay in milliseconds for each status dot

    // Loop through each dot (status-dot1 to status-dot4) and apply the delay
    for (let i = 1; i <= 4; i++) {
      const delay = delays[i - 1]; // Get the delay for each dot
      setInterval(() => {
        changeStatus(`status-dot${i}`, `status-text${i}`);
      }, 40000 + delay); // 40 seconds plus the specific delay for each dot
    }
  }

  // Initialize the status dots on page load
  initStatusDots();
});
// Define attendance data for each employee
const employeeData = {
  giray: {
    name: "John Mark Giray",
    profileImage: "image/GirayProf.jpg",
    timeIn: "09:00",
    timeOut: "17:00",
    lateTime: "0 mins",
    overtime: "30 mins",
    deductions: "₱5.00",
  },
  lacsi: {
    name: "Rhyzon Lacsi",
    profileImage: "image/LacsiPRof.jpg",
    timeIn: "09:30",
    timeOut: "17:30",
    lateTime: "30 mins",
    overtime: "0 mins",
    deductions: "₱0",
  },
  trono: {
    name: "Jaime Trono",
    profileImage: "image/TronoProf.jpg",
    timeIn: "10:00",
    timeOut: "18:00",
    lateTime: "60 mins",
    overtime: "0 mins",
    deductions: "₱10.00",
  },
};

// Function to toggle the visibility of the attendance info for each employee
function toggleAttendanceInfo(employeeId) {
  const attendanceInfo = document.getElementById(
    employeeId + "-attendance-info"
  );
  const button = document.querySelector("#" + employeeId + "-container button");

  if (attendanceInfo.style.display === "block") {
    attendanceInfo.style.display = "none";
    button.innerText = "Informations";
  } else {
    attendanceInfo.style.display = "block";
    button.innerText = "Close";
  }
}

// Function to update the attendance info for each employee
function updateAttendanceInfo() {
  for (const employeeId in employeeData) {
    const employee = employeeData[employeeId];

    // Update profile image and name
    document.getElementById(employeeId + "-profile-img").src =
      employee.profileImage;
    document.getElementById(employeeId + "-name").textContent = employee.name;

    // Update attendance details
    document.getElementById(employeeId + "-employee-name").textContent =
      employee.name;
    document.getElementById(employeeId + "-time-in").textContent =
      employee.timeIn;
    document.getElementById(employeeId + "-time-out").textContent =
      employee.timeOut;
    document.getElementById(employeeId + "-late-time").textContent =
      employee.lateTime;
    document.getElementById(employeeId + "-overtime").textContent =
      employee.overtime;
    document.getElementById(employeeId + "-deductions").textContent =
      employee.deductions;
  }
}

// Initial call to populate data for all employees
updateAttendanceInfo();
