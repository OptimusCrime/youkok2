import React, { Component } from 'react';
import { connect } from 'react-redux';

import { BoxWrapper } from "../../../components/box-wrapper";
import { CourseItem } from "../../../components/course-item";
import { MostPopularDropdown } from "../../../components/most-popular/dropdown";
import { formatNumber } from "../../../../common/utils";
import { DELTA_POST_POPULAR_COURSES } from "../../../consts";

class BoxMostPopularCourses extends Component {

  constructor(props) {
    super(props);

    this.state = {
      open: false
    };

    this.toggleDropdown = this.toggleDropdown.bind(this);
  }

  toggleDropdown() {
    this.setState({
      open: !this.state.open
    });
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
};

export default connect(mapStateToProps, mapDispatchToProps)(BoxMostPopularCourses);