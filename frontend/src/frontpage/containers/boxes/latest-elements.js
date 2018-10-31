import React, { Component } from 'react';
import { connect } from 'react-redux';

import { BoxWrapper } from "../../../common/components/box-wrapper";
import { ElementItem } from "../../../common/components/element-item";
import { ItemTimeAgo } from "../../components/item-time-ago";
import { fromDatabaseDateToJavaScriptDate } from "../../../common/utils";

class BoxLatestElements extends Component {

  render() {

    const {
      isLoading,
      latestElements,
    } = this.props;

    return (
      <div className="col-xs-12 col-sm-6 frontpage-box">
        <BoxWrapper
          title="Nyeste"
          isLoading={isLoading}
          isEmpty={!isLoading && latestElements.length === 0}
        >
          {!isLoading && latestElements.map((element, index) =>
            <ElementItem element={element} key={index} additional={<ItemTimeAgo datetime={fromDatabaseDateToJavaScriptDate(element.added)} /> } /> )
          }
        </BoxWrapper>
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