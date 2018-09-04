import React, { Component } from 'react';
import { connect } from 'react-redux';

import { CourseItem } from '../../../components/course-item';
import { EmptyItem } from '../../../components/empty-item';
import { StencilItemList } from '../../../components/stencilate/item-list';
import { updateFrontpage as updateFrontpageDispatch } from "../../../redux/frontpage/actions";
import { FRONTPAGE_RESET_HISTORY_TYPE } from "../../../consts";

class BoxLastVisitedContainer extends Component {

  render() {

    const {
      isLoading,
      userHistory,

      updateFrontpage
    } = this.props;

    return (
      <div className="col-xs-12 col-sm-6 frontpage-box">
        <div className="list-header">
          <h2>Dine siste besøkte fag</h2>
          {(!isLoading && userHistory.length > 0) &&
            <a
              href="#"
              className="frontpage-box-clear"
              onClick={() => updateFrontpage(FRONTPAGE_RESET_HISTORY_TYPE)}
            >Fjern historikk</a>
          }
        </div>
        <ul className="list-group">
          {isLoading && <StencilItemList size={1} />}
          {!isLoading && userHistory.map((course, index) => <CourseItem course={course} key={index} /> )}
          {!isLoading && userHistory.length === 0 && <EmptyItem text='Du har ikke besøkt noen fag' />}
        </ul>
      </div>
    );
  }
}

const mapStateToProps = ({ frontpage }) => ({
  userHistory: frontpage.user_history
});

const mapDispatchToProps = {
  updateFrontpage: updateFrontpageDispatch
};

export default connect(mapStateToProps, mapDispatchToProps)(BoxLastVisitedContainer);