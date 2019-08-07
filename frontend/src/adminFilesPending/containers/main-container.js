import React from 'react';
import {connect} from 'react-redux';

import {loading} from "../../common/utils";
import {Content} from "../components/content";

const AdminFilesPendingMainContainer = ({started, finished, failed, data}) => {

  if (failed) {
    return (
      <p>Her gikk visst noe galt...</p>
    );
  }

  const isLoading = loading(started, finished);

  if (isLoading) {
    return null;
  }

  return data.map(entry => (
   <Content
     key={entry.content.id}
     content={entry.content}
     pending={entry.pending}
   />
  ));
};

const mapStateToProps = ({pending}) => ({
  started: pending.started,
  finished: pending.finished,
  failed: pending.failed,
  data: pending.data,
});

export default connect(mapStateToProps, {})(AdminFilesPendingMainContainer);
