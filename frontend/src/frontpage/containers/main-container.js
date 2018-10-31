import React from 'react';
import { connect } from 'react-redux';

import InfoBoxesContainer from './info-boxes-container';
import MainBoxesContainer from './main-boxes-container';

const MainContainer = ({ failed }) => {

  if (failed) {
    return (
      <p>Vi har visst litt tekniske problemer her...</p>
    );
  }

  return (
    <React.Fragment>
      <InfoBoxesContainer />
      <MainBoxesContainer />
    </React.Fragment>
  );
};

const mapStateToProps = ({ frontpage }) => ({
  failed: frontpage.failed,
});

export default connect(mapStateToProps, {})(MainContainer);