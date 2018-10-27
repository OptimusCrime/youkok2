import React, { Component } from 'react';
import { connect } from 'react-redux';

import { BoxWrapper } from "../../../components/box-wrapper";
import { CourseItem } from "../../../components/course-item";
import { MostPopularDropdown } from "../../../components/most-popular/dropdown";
import { formatNumber } from "../../../../common/utils";
import { DELTA_POST_POPULAR_COURSES } from "../../../consts";
import { updateFrontpage as updateFrontpageDispatch } from "../../../redux/frontpage/actions";

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
    const { updateFrontpage } = this.props;

    updateFrontpage(DELTA_POST_POPULAR_COURSES, delta);
  }

  render() {

    const {
      isLoading,
      coursesMostPopular,
      userPreferences,
    } = this.props;

    const selectedButton = userPreferences[DELTA_POST_POPULAR_COURSES];

    return (
      <BoxWrapper
        title="Mest populære fag"
        titleInline={true}
        isLoading={isLoading}
        isEmpty={!isLoading && coursesMostPopular.length === 0}
        dropdown={
          <MostPopularDropdown
            selectedButton={selectedButton}
            open={this.state.open}
            toggleDropdown={this.toggleDropdown}
            changeDelta={this.changeDelta}
          />
        }
      >
        {!isLoading && coursesMostPopular.map((course, index) =>
          <CourseItem course={course} key={index} additional={<span>[ca. {formatNumber(course.download_estimate)}]</span>} /> )
        }
      </BoxWrapper>
    );
  }
}

const mapStateToProps = ({ frontpage }) => ({
  coursesMostPopular: frontpage.courses_most_popular,
  userPreferences: frontpage.user_preferences,
});

const mapDispatchToProps = {
  updateFrontpage: updateFrontpageDispatch
};

export default connect(mapStateToProps, mapDispatchToProps)(BoxMostPopularCourses);