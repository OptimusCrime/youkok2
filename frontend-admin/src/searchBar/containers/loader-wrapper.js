import React from "react";
import {connect} from "react-redux";

import MainContainer from "./main-container";
import {UPDATE_COURSES_LOADED} from "../redux/form/constants";
import {getCourses, refreshCourses} from "../../common/coursesLookup";
import {MODE_ADMIN, MODE_SITE} from "../constants";

const LoaderWrapperContainer = props => {
  const courses = getCourses();

  // What a mess
  if (props.mode === MODE_SITE && courses === null) {
    refreshCourses(null)
      .then(() => {
        props.updateCoursesLoaded();
      });
  }
  else if (props.mode === MODE_SITE) {
    props.updateCoursesLoaded();
  }

  if ((props.mode === MODE_SITE && props.loaded) || (props.mode === MODE_ADMIN && props.admin_loaded)) {
    return <MainContainer />
  }

  return <div/>;
}

const mapStateToProps = ({form, config}) => ({
  loaded: form.loaded,
  mode: config.mode,
  admin_loaded: config.admin_loaded,
});

const mapDispatchToProps = {
  updateCoursesLoaded: () => ({ type: UPDATE_COURSES_LOADED }),
};

export default connect(mapStateToProps, mapDispatchToProps)(LoaderWrapperContainer);
