import React from 'react';
import {connect} from 'react-redux';

import {Header} from '../components/header';
import {CourseItem} from "../components/courseItem";
import {COURSES_CHANGE_ORDER, COURSES_CHANGE_PAGE} from "../redux/courses/constants";
import {COURSES_PER_PAGE} from "../constants";
import {Navigation} from "../components/navigation";

const MainContainer = ({sortColumn, sortOrder, courses, page, changeOrder, changePage }) => {

  if (!window.COURSES_LOOKUP) {
    return (
      <div className="alert alert-warning" role="alert">Kunne ikke laste fagene</div>
    );
  }

  // Pages are 0 indexed...
  const NUMBER_OF_PAGES = Math.ceil(window.COURSES_LOOKUP.length / COURSES_PER_PAGE) - 1;

  return (
    <div className="course-container">
      <Navigation
        changePage={changePage}
        page={page}
        numberOfPages={NUMBER_OF_PAGES}
      />
      <div className="course-list">
        <Header
          sortColumn={sortColumn}
          sortOrder={sortOrder}
          changeOrder={changeOrder}
        />
        {courses.map((course, index) =>
          <CourseItem
            course={course}
            key={index}
          />
        )}
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
  sortColumn: courses.sortColumn,
  sortOrder: courses.sortOrder,
  page: courses.page,
  courses: courses.courses,
});

const mapDispatchToProps = {
  changeOrder: (column, order) => ({type: COURSES_CHANGE_ORDER, sortColumn: column, sortOrder: order}),
  changePage: page => ({type: COURSES_CHANGE_PAGE, page}),
};

export default connect(mapStateToProps, mapDispatchToProps)(MainContainer);
