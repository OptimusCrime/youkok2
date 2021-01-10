import React, { Component } from 'react';
import { connect } from 'react-redux';

import { BoxWrapper } from "../../../../common/components/box-wrapper";
import { CourseItem } from "../../../components/course-item";
import { MostPopularDropdown } from "../../../components/most-popular/dropdown";
import {formatNumber, loading} from "../../../../common/utils";
import {
  DEFAULT_MOST_POPULAR_COURSES_DELTA,
  DELTA_POST_POPULAR_COURSES,
} from "../../../consts";
import {updateFrontpagePopularCourses as updateFrontpagePopularCoursesDispatch } from "../../../redux/popular_courses/actions";
import {EmptyItem} from "../../../../common/components/empty-item";
import {getItem} from "../../../../common/local-storage";

class BoxMostPopularCourses extends Component {

  constructor(props) {
    super(props);

    this.state = {
      open: false
    };

    this.toggleDropdown = this.toggleDropdown.bind(this);
    this.changeDelta = this.changeDelta.bind(this);
  }

  toggleDropdown() {
    this.setState({
      open: !this.state.open
    });
  }

  changeDelta(delta) {
    const { updateFrontpagePopularCourses } = this.props;

    updateFrontpagePopularCourses(delta);
  }

  render() {

    const {
      started,
      finished,
      failed,
      courses,
      preference
    } = this.props;

    const isLoading = loading(started, finished);

    if (failed) {
      return (
        <div className="col-xs-12 col-sm-6 frontpage-box">
          <BoxWrapper
            title="Mest populære fag"
            titleInline={false}
            isLoading={false}
            isEmpty={false}

          >
            <EmptyItem text="Kunne ikke hente mest populære fag" />
          </BoxWrapper>
        </div>
      );
    }

    const selected = getItem(DELTA_POST_POPULAR_COURSES) || DEFAULT_MOST_POPULAR_COURSES_DELTA;

    return (
      <div className="col-xs-12 col-sm-6 frontpage-box">
        <BoxWrapper
          title="Mest populære fag"
          titleInline={true}
          isLoading={isLoading}
          isEmpty={!isLoading && courses.length === 0}
          dropdown={
            <MostPopularDropdown
              selectedButton={selected}
              open={this.state.open}
              toggleDropdown={this.toggleDropdown}
              changeDelta={this.changeDelta}
            />
          }
        >
          {!isLoading && courses.map((course, index) =>
            <CourseItem course={course} key={index} additional={<span>[ca. {formatNumber(course.download_estimate)}]</span>} /> )
          }
        </BoxWrapper>
      </div>
    );
  }
}

const mapStateToProps = ({ popularCourses }) => ({
  started: popularCourses.started,
  finished: popularCourses.finished,
  failed: popularCourses.failed,
  courses: popularCourses.courses,
  preference: popularCourses.preference,
});

const mapDispatchToProps = {
  updateFrontpagePopularCourses: updateFrontpagePopularCoursesDispatch
};

export default connect(mapStateToProps, mapDispatchToProps)(BoxMostPopularCourses);
