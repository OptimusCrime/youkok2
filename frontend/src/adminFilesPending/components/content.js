import React from 'react';

import ContentContainer from '../containers/content-container';
import PendingContainer from "../containers/pending-container";

export const Content = ({ content, pending }) => (
  <div className="row">
    <div className="col-md-6">
      <ContentContainer
        content={content}
      />
    </div>
    <div className="col-md-6">
      <PendingContainer
        pending={pending}
      />
    </div>
  </div>
);

