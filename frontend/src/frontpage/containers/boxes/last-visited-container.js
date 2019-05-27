import React, { Component } from 'react';
import { connect } from 'react-redux';

import { BoxWrapper } from "../../../common/components/box-wrapper";
import { CourseItem } from '../../components/course-item';
import { ItemTimeAgo } from "../../components/item-time-ago";
import { fromDatabaseDateToJavaScriptDate } from "../../../common/utils";
import {EmptyItem} from "../../../common/components/empty-item";

class BoxLastVisitedContainer extends Component {

  render() {

    const {
      failed,
      isLoading,
      coursesLastVisited,
    } = this.props;

    if (failed) {
      return (
        <div className="col-xs-12 col-sm-6 frontpage-box">
          <BoxWrapper
            title="Siste besøkte fag"
            titleInline={false}
            isLoading={false}
            isEmpty={false}

          >
            <EmptyItem text="Kunne ikke hente siste besøkte fag" />
          </BoxWrapper>
        </div>
      );
    }

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
