import React, { Component } from 'react';
import { connect } from 'react-redux';

import { BoxWrapper } from "../../../common/components/box-wrapper";
import { ElementItem } from "../../../common/components/element-item";
import { ItemTimeAgo } from "../../components/item-time-ago";
import { fromDatabaseDateToJavaScriptDate } from "../../../common/utils";


class BoxLastDownloadedContainer extends Component {

  render() {

    const {
      isLoading,
      lastDownloaded,
    } = this.props;

    return (
      <div className="col-xs-12 col-sm-6 frontpage-box">
        <BoxWrapper
          title="Siste nedlastninger"
          isLoading={isLoading}
          isEmpty={!isLoading && lastDownloaded.length === 0}
        >
          {!isLoading && lastDownloaded.map((element, index) =>
            <ElementItem element={element} key={index} additional={<ItemTimeAgo datetime={fromDatabaseDateToJavaScriptDate(element.downloaded_time)} /> } /> )
          }
        </BoxWrapper>
      </div>
    );
  }
}

const mapStateToProps = ({ frontpage }) => ({
  lastDownloaded: frontpage.last_downloaded
});

const mapDispatchToProps = {
};

export default connect(mapStateToProps, mapDispatchToProps)(BoxLastDownloadedContainer);