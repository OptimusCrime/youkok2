import React from "react";

export const CourseItem = ({ course }) => (
  <a className={`course-row ${course.empty ? 'course-row__empty': ''}`} href={course.url}>
    <div className="course-row__code">
      <strong>{course.code}</strong>
    </div>
    <div className="course-row__name">
      {course.name}
    </div>
  </a>
);
