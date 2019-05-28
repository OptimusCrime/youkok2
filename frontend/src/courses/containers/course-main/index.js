import React from 'react';
import {connect} from 'react-redux';

import CourseForm from '../course-form';
import {Header} from '../../components/header';
import {CourseItem} from "../../components/course-item";
import {COURSES_CHANGE_ORDER, COURSES_CHANGE_PAGE} from "../../redux/courses/constants";
import {COURSES_PER_PAGE} from "../../constants";
import {Navigation} from "../../components/navigation";
import {CourseItemEmpty} from "../../components/course-item-empty";

const CourseMain = ({column, order, courses, page, changeOrder, changePage}) => {

  if (!window.COURSES_LOOKUP) {
    return (
      <div className="alert alert-warning" role="alert">Kunne ikke laste fagene</div>
    );
  }

  // Pages are 0 indexed...
  const NUMBER_OF_PAGES = Math.ceil(courses.length / COURSES_PER_PAGE) - 1;

  const coursesCurrentPage = courses.slice(page * COURSES_PER_PAGE, (page + 1) * COURSES_PER_PAGE);

  return (
    <div className="course-container">
      <CourseForm/>
      <Navigation
        changePage={changePage}
        page={page}
        numberOfPages={NUMBER_OF_PAGES}
      />
      <div className="course-list">
        <Header
          column={column}
          order={order}
          changeOrder={changeOrder}
          numCourses={courses.length}
        />
        {coursesCurrentPage.length > 0 && coursesCurrentPage.map((course, index) =>
          <CourseItem
            course={course}
            key={index}
          />
        )}
        {coursesCurrentPage.length === 0 &&
        <CourseItemEmpty/>
        }
      </div>
      <Navigation
        changePage={changePage}
        page={page}
        numberOfPages={NUMBER_OF_PAGES}
      />
    </div>
  );
};

const mapStateToProps = ({courses}) => ({
  column: courses.column,
  order: courses.order,
  page: courses.page,
  courses: courses.courses,
});

const mapDispatchToProps = {
  changeOrder: (column, order) => ({type: COURSES_CHANGE_ORDER, column, order}),
  changePage: page => ({type: COURSES_CHANGE_PAGE, page}),
};

export default connect(mapStateToProps, mapDispatchToProps)(CourseMain);
