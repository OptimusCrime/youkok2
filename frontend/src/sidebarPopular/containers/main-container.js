import React from 'react';
import { connect } from 'react-redux';

import { BoxWrapper } from "../../common/components/box-wrapper";
import { formatNumber, loading } from "../../common/utils";
import {ElementItem} from "../../common/components/element-item";

const MainContainer = ({ started, finished, failed, data }) => {

  if (failed) {
    return (
      <p>Vi har visst litt tekniske problemer her...</p>
    );
  }

  const isLoading = loading(started, finished);

  return (
    <BoxWrapper
      title="Denne måneden"
      titleInline={false}
      isLoading={isLoading}
      isEmpty={!isLoading && data.length === 0}
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