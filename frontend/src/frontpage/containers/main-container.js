import React, { Component } from 'react';
import { connect } from 'react-redux';

import InfoBoxesContainer from './info-boxes-container';
import MainBoxesContainer from './main-boxes-container';
import { fetchFrontpage } from '../redux/frontpage/actions';

class MainContainer extends Component {


  componentDidMount() {
    this.props.fetchFrontpage();
  }

  render() {

    const {
      failed
    } = this.props;

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
  }
}

const mapStateToProps = ({ }) => ({
});

const mapDispatchToProps = {
  fetchFrontpage
};

export default connect(mapStateToProps, mapDispatchToProps)(MainContainer);