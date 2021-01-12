import React from "react";
import {connect} from "react-redux";

import MainContainer from "./main-container";
import {UPDATE_COURSES_LOADED} from "../redux/form/constants";
import {getCourses, refreshCourses} from "../../common/coursesLookup";

const LoaderWrapperContainer = props => {
  const courses = getCourses();

  // What a mess
  if (courses === null) {
    refreshCourses(null)
      .then(() => {
        props.updateCoursesLoaded();
      });
  }
  else {
    props.updateCoursesLoaded();
  }

  if (props.loaded) {
    return <MainContainer />
  }

  return <div/>;
}

const mapStateToProps = ({form}) => ({
  loaded: form.loaded,
});

const mapDispatchToProps = {
  updateCoursesLoaded: () => ({ type: UPDATE_COURSES_LOADED }),
};

export default connect(mapStateToProps, mapDispatchToProps)(LoaderWrapperContainer);
