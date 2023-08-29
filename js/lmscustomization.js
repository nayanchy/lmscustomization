let totalCourse = "";

if (document.getElementById("total_course")) {
  totalCourse = +document.getElementById("total_course").innerText;
}
const completedCourseHtml = document.getElementById("completed_course");

function updateProgressBar(percentage) {
  const progressBarFill = document.getElementById("progress");
  const percentageDynamic = document.getElementById("percentage_dynamic");
  if (progressBarFill && percentageDynamic) {
    progressBarFill.style.width = `${percentage}%`;
    percentageDynamic.textContent = `${percentage}%`;
  }
}

function getPercentage() {
  const courseList = document.querySelectorAll(".course_list");
  const courseLists = Array.from(courseList);
  let completedCourse = 0;
  if (courseList) {
    courseLists.forEach((course, i) => {
      if (course.classList.contains("mark_completed")) {
        completedCourse++;
      }
    });
    const percentage = Math.floor((completedCourse / totalCourse) * 100);
    if (completedCourseHtml) {
      completedCourseHtml.innerHTML = completedCourse;
    }
    // Example usage: updateProgressBar(75); // Will fill 75% of the progress bar
    updateProgressBar(percentage);
  }
}

function markCompletedFrontend() {
  const courseList = document.querySelectorAll(".course_list");
  const courseLists = Array.from(courseList);
  courseLists.forEach((course, i) => {
    if (course.classList.contains("mark_completed")) {
      course.querySelector(
        ".course_list_no"
      ).innerHTML = `<span class="material-symbols-outlined mark_completed_icon md-18">done</span>`;
    } else {
      course.querySelector(".course_list_no").innerHTML = `${i + 1}`;
    }
  });
}
getPercentage();
markCompletedFrontend();

jQuery(document).ready(function ($) {
  $("body").on("click", "#mark-complete-button", function () {
    var postId = $(this).data("post-id");

    $.ajax({
      type: "POST",
      url: lmscustomization_ajax_object.ajaxurl,
      data: {
        action: "mark_complete",
        post_id: postId,
      },
      success: function (response) {
        if (response.success) {
          var buttonText =
            response.data === "Completed" ? "Completed" : "Mark as Complete";
          $("#mark-complete-button").text(buttonText);

          // Toggle the "mark_completed" class based on the response
          const courseList = document.querySelectorAll(".course_list");
          const courseLists = Array.from(courseList);

          courseLists.forEach((course, i) => {
            const listPostId = +course.getAttribute("data-post-id");
            if (postId === listPostId && response.data === "Completed") {
              course.classList.add("mark_completed");
              course.querySelector(
                ".course_list_no"
              ).innerHTML = `<span class="material-symbols-outlined mark_completed_icon md-18">done</span>`;
            } else if (
              postId === listPostId &&
              response.data === "Incomplete"
            ) {
              course.classList.remove("mark_completed");
              course.querySelector(".course_list_no").innerHTML = `${i + 1}`;
            }
          });

          getPercentage();
        }
      },
      error: function () {
        console.log("An error occurred while processing the AJAX request.");
      },
    });
  });
});
