import React from 'react';
import {connect} from 'react-redux';

import {BoxWrapper, TITLE_SIZE_H3} from "../../common/components/box-wrapper";
import {loading} from "../../common/utils";
import {EmptyItem} from "../../common/components/empty-item";
import {TextItem} from "../../common/components/text-item";

const HISTORY_STENCIL_SIZE = 5;

const MainContainer = ({started, finished, failed, data, data_failed }) => {

  if (data_failed) {
    return <div/>;
  }

  if (failed) {
    return (
      <BoxWrapper
        title="Historikk"
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
      title="Historikk"
      titleInline={false}
      isLoading={isLoading}
      isEmpty={!isLoading && data.length === 0}
      stencil_size={HISTORY_STENCIL_SIZE}
      titleSize={TITLE_SIZE_H3}
    >
      {!isLoading && data.map((element, index) =>
        <TextItem
          key={index}
          text={element}
        />
      )}
    </BoxWrapper>
  );
};

const mapStateToProps = ({history, archive}) => ({
  started: history.started,
  finished: history.finished,
  failed: history.failed,
  data: history.data,

  data_failed: archive.data_failed,
});

export default connect(mapStateToProps)(MainContainer);
