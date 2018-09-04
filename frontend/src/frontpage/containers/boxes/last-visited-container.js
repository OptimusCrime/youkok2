import React, { Component } from 'react';
import { connect } from 'react-redux';

import { CourseItem } from '../../components/course-item';
import { EmptyItem } from '../../components/empty-item';
import { StencilItemList } from '../../components/stencilate/item-list';

class BoxLastVisitedContainer extends Component {

  render() {

    const {
      isLoading,
      coursesLastVisited,
    } = this.props;

    return (
      <div className="col-xs-12 col-sm-6 frontpage-box">
        <div className="list-header">
          <h2>Siste besøkte fag</h2>
        </div>
        <ul className="list-group">
          {isLoading && <StencilItemList size={10} />}
          {!isLoading && coursesLastVisited.map((course, index) => <CourseItem course={course} key={index} /> )}
          {!isLoading && coursesLastVisited.length === 0 && <EmptyItem text='Det er ingenting her' />}
        </ul>
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