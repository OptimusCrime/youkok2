import React from "react";
import {connect} from "react-redux";

import CourseMain from '../course-main';
import {getCourses, refreshCourses} from "../../../common/coursesLookup";
import {COURSES_LOOKUP_LOADED} from "../../redux/courses/constants";

const LoaderWrapperContainer = props => {
  const courses = getCourses();

  // What a mess
  if (courses === null) {
    refreshCourses(null)
      .then(() => {
        props.updateCoursesLoaded();
      });
  }

  if (props.loaded) {
    return <CourseMain />;
  }

  return (
    <p>Laster...</p>
  );
}

const mapStateToProps = ({courses}) => ({
  loaded: courses.loaded,
});

const mapDispatchToProps = {
  updateCoursesLoaded: () => ({ type: COURSES_LOOKUP_LOADED }),
};

export default connect(mapStateToProps, mapDispatchToProps)(LoaderWrapperContainer);
