import React, { Component } from 'react';
import { connect } from 'react-redux';

import { EmptyItem } from '../../components/empty-item';
import { StencilItemList } from '../../components/stencilate/item-list';
import {ElementItem} from "../../components/element-item";

const LatestElementTimeStamp = ({ time }) => (
  <span className="moment-timestamp help" data-toggle="tooltip" title="13. mar 2018 @ 13:38:17" data-ts={time}>Laster...</span>
);

class BoxLatestElements extends Component {

  render() {

    const {
      isLoading,
      latestElements,
    } = this.props;

    return (
      <div className="col-xs-12 col-sm-6 frontpage-box">
        <div className="list-header">
          <h2>Nyeste</h2>
        </div>
        <ul className="list-group">
          {isLoading && <StencilItemList size={10} />}
          {!isLoading && latestElements.map((element, index) => <ElementItem element={element} key={index} additional={<LatestElementTimeStamp time={element.added} /> } /> )}
          {!isLoading && latestElements.length === 0 && <EmptyItem text='Det er ingenting her' />}
        </ul>
      </div>
    );
  }
}

const mapStateToProps = ({ frontpage }) => ({
  latestElements: frontpage.latest_elements
});

const mapDispatchToProps = {
};

export default connect(mapStateToProps, mapDispatchToProps)(BoxLatestElements);