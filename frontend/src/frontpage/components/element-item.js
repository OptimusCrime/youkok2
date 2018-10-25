import React from 'react';

const CourseParent = ({ course }) => (
  <React.Fragment>
    <a href={course.url} title={course.courseName} data-placement="top" data-toggle="tooltip">
      {course.courseCode}
    </a>&nbsp;
  </React.Fragment>
);

const ElementParentPath = ({ parent, course }) => (
  <React.Fragment>
    <a href={parent.url}>
      {parent.name}
    </a>,&nbsp;
    <CourseParent course={course} />&nbsp;
  </React.Fragment>
);


export const ElementItem = ({ element, additional }) => (
  <li className="list-group-item">
    <a href={element.url}>
      {element.name}
    </a>&nbsp;@&nbsp;
    {element.parent.id === element.course.id && <CourseParent course={element.parent} /> }
    {element.parent.id !== element.course.id && <ElementParentPath parent={element.parent} course={element.course} /> }
    {additional}
  </li>
);