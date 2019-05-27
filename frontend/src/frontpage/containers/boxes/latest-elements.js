import React, { Component } from 'react';
import { connect } from 'react-redux';

import { BoxWrapper } from "../../../common/components/box-wrapper";
import { ElementItem } from "../../../common/components/element-item";
import { ItemTimeAgo } from "../../components/item-time-ago";
import { fromDatabaseDateToJavaScriptDate } from "../../../common/utils";
import {EmptyItem} from "../../../common/components/empty-item";

class BoxLatestElements extends Component {

  render() {

    const {
      failed,
      isLoading,
      latestElements,
    } = this.props;

    if (failed) {
      return (
        <div className="col-xs-12 col-sm-6 frontpage-box">
          <BoxWrapper
            title="Nyeste"
            titleInline={false}
            isLoading={false}
            isEmpty={false}

          >
            <EmptyItem text="Kunne ikke hente neste" />
          </BoxWrapper>
        </div>
      );
    }

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
