import React from 'react';
import {connect} from 'react-redux';

import {COURSE_CHANGE_CHECKBOX, COURSES_UPDATE_SEARCH} from "../../redux/courses/constants";

const CourseForm = ({ search, showOnlyNotEmpty, updateSearchField, updateCheckBox }) => {
  return (
    <div className="form-group course-form">
      <div className="course-form__input">
    <input
      type="text"
      placeholder="Filtrer fag"
      className="form-control"
      onChange={e => {
        updateSearchField(e.target.value);
      }}
      value={search}
    />
      </div>
      <div className="course-form__checkbox">
      <div className="checkbox">
        <label>
          <input type="checkbox"
                 checked={showOnlyNotEmpty}
                 onChange={updateCheckBox}
          /> Vis bare fag med innhold
        </label>
      </div>
      </div>
    </div>
  );
};

const mapStateToProps = ({courses}) => ({
  search: courses.search,
  showOnlyNotEmpty: courses.showOnlyNotEmpty,
});

const mapDispatchToProps = {
  updateSearchField: value => ({type: COURSES_UPDATE_SEARCH, value}),
  updateCheckBox: () => ({type: COURSE_CHANGE_CHECKBOX }),
};

export default connect(mapStateToProps, mapDispatchToProps)(CourseForm);
