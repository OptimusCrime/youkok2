import React, { Component } from 'react';
import { connect } from 'react-redux';

import { BoxWrapper } from "../../components/box-wrapper";
import { ElementItem } from "../../components/element-item";
import { ItemTimeAgo } from "../../components/item-time-ago";
import { fromDatabaseDateToJavaScriptDate } from "../../../common/utils";


class BoxLastDownloadedContainer extends Component {

  render() {

    const {
      isLoading,
      lastDownloaded,
    } = this.props;

    return (
      <BoxWrapper
        title="Siste nedlastninger"
        isLoading={isLoading}
        isEmpty={!isLoading && lastDownloaded.length === 0}
      >
        {!isLoading && lastDownloaded.map((element, index) =>
          <ElementItem element={element} key={index} additional={<ItemTimeAgo datetime={fromDatabaseDateToJavaScriptDate(element.downloaded_time)} /> } /> )
        }
      </BoxWrapper>
    );
  }
}

const mapStateToProps = ({ frontpage }) => ({
  lastDownloaded: frontpage.last_downloaded
});

const mapDispatchToProps = {
};

export default connect(mapStateToProps, mapDispatchToProps)(BoxLastDownloadedContainer);