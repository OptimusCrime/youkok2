import React, { Component } from 'react';
import { connect } from 'react-redux';

import { BoxWrapper } from "../../../common/components/box-wrapper";
import { CourseItem } from '../../components/course-item';
import { ItemTimeAgo } from "../../components/item-time-ago";
import { fromDatabaseDateToJavaScriptDate } from "../../../common/utils";

class BoxLastVisitedContainer extends Component {

  render() {

    const {
      isLoading,
      coursesLastVisited,
    } = this.props;

    return (
      <div className="col-xs-12 col-sm-6 frontpage-box">
        <BoxWrapper
          title="Siste besøkte fag"
          isLoading={isLoading}
          isEmpty={!isLoading && coursesLastVisited.length === 0}
        >
          {!isLoading && coursesLastVisited.map((course, index) =>
            <CourseItem course={course} key={index} additional={<ItemTimeAgo datetime={fromDatabaseDateToJavaScriptDate(course.last_visited)} /> } /> )
          }
        </BoxWrapper>
      </div>
    );
  }
}

const mapStateToProps = ({ frontpage }) => ({
  coursesLastVisited: frontpage.courses_last_visited
});

const mapDispatchToProps = {
};

export default connect(mapStateToProps, mapDispatchToProps)(BoxLastVisitedContainer);