import React from 'react';
import { connect } from 'react-redux';

import {BoxWrapper, TITLE_SIZE_H3} from "../../common/components/box-wrapper";
import { EmptyItem } from "../../common/components/empty-item";
import {formatNumber, loading} from "../../common/utils";
import { ElementItem } from "../../common/components/element-item";

const MainContainer = ({ started, finished, failed, data }) => {

  if (failed) {
    return (
      <BoxWrapper
        title="Populære denne uka"
        titleInline={false}
        isLoading={false}
        isEmpty={false}
      >
        <EmptyItem
          text="Vi har visst litt tekniske problemer her..."
        />
      </BoxWrapper>
    );
  }

  const isLoading = loading(started, finished);

  return (
    <BoxWrapper
      title="Populære denne uka"
      titleInline={false}
      isLoading={isLoading}
      isEmpty={!isLoading && data.length === 0}
      titleSize={TITLE_SIZE_H3}
    >
      {!isLoading && data.map((element, index) =>
        <ElementItem element={element} key={index} additional={<span>[{formatNumber(element.downloads)}]</span>} /> )
      }
    </BoxWrapper>
  );
};

const mapStateToProps = ({ elements }) => ({
  started: elements.started,
  finished: elements.finished,
  failed: elements.failed,
  data: elements.data,
});

export default connect(mapStateToProps, {})(MainContainer);
