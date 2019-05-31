import React, { Component } from 'react';
import { connect } from 'react-redux';

import { BoxWrapper } from "../../../common/components/box-wrapper";
import { ElementItem } from "../../../common/components/element-item";
import { ItemTimeAgo } from "../../components/item-time-ago";
import {fromDatabaseDateToJavaScriptDate, loading} from "../../../common/utils";
import {EmptyItem} from "../../../common/components/empty-item";


class BoxLastDownloadedContainer extends Component {

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
            title="Siste nedlastninger"
            titleInline={false}
            isLoading={false}
            isEmpty={false}
          >
            <EmptyItem text="Kunne ikke hente siste nedlastninger" />
          </BoxWrapper>
        </div>
      );
    }

    return (
      <div className="col-xs-12 col-sm-6 frontpage-box">
        <BoxWrapper
          title="Siste nedlastninger"
          isLoading={isLoading}
          isEmpty={!isLoading && elements.length === 0}
        >
          {!isLoading && elements.map((element, index) =>
            <ElementItem element={element} key={index} additional={<ItemTimeAgo datetime={fromDatabaseDateToJavaScriptDate(element.downloaded_time)} /> } /> )
          }
        </BoxWrapper>
      </div>
    );
  }
}

const mapStateToProps = ({ lastDownloaded }) => ({
  started: lastDownloaded.started,
  finished: lastDownloaded.finished,
  failed: lastDownloaded.failed,
  elements: lastDownloaded.elements,
});

export default connect(mapStateToProps, {})(BoxLastDownloadedContainer);
