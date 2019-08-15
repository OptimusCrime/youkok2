import React, { Component } from 'react';
import { connect } from 'react-redux';

import { BoxWrapper } from "../../../common/components/box-wrapper";
import { ElementItem } from "../../../common/components/element-item";
import { ItemTimeAgo } from "../../components/item-time-ago";
import {fromDatabaseDateToJavaScriptDate, loading} from "../../../common/utils";
import {EmptyItem} from "../../../common/components/empty-item";

class BoxNewestContainer extends Component {

  render() {

    const {
      started,
      finished,
      failed,
      elements,
    } = this.props;

    const isLoading = loading(started, finished);

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
          isEmpty={!isLoading && elements.length === 0}
        >
          {!isLoading && elements.map((element, index) =>
            <ElementItem element={element} key={index} additional={<ItemTimeAgo datetime={fromDatabaseDateToJavaScriptDate(element.added)} /> } /> )
          }
        </BoxWrapper>
      </div>
    );
  }
}

const mapStateToProps = ({ newest }) => ({
  started: newest.started,
  finished: newest.finished,
  failed: newest.failed,
  elements: newest.elements,
});

export default connect(mapStateToProps)(BoxNewestContainer);
