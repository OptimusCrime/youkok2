import React from 'react';

export const CourseItem = ({ course, additional }) => (
  <li className="list-group-item">
    <a
      href={course.url}
    >
      <strong>{course.courseCode}</strong>&nbsp;&mdash;&nbsp;
      {course.courseName}
    </a>&nbsp;{additional}
  </li>
);