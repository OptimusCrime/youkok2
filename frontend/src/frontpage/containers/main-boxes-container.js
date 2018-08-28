import React, { Component } from 'react';
import { connect } from 'react-redux';

import BoxLastVisitedContainer from './boxes/last-visited-container';

import { loading } from '../utilities/utils';

class InfoBoxesContainer extends Component {

  render() {

    const {
      started,
      finished,
    } = this.props;

    const isLoading = loading(started, finished);

    return (
      <React.Fragment>
        <div className="row">
          <BoxLastVisitedContainer isLoading={isLoading} />
          <div className="col-xs-12 col-sm-6 frontpage-box">
            <div className="list-header">
              <h2>Favoritter</h2>
              <a href="#" className="frontpage-box-clear" id="frontpage-empty-favorites">Fjern favoritter</a>
            </div>
            <ul className="list-group" id="favorites-list">
              <li className="list-group-item"><em>Du har ingen favoritter</em></li>
            </ul>
          </div>
        </div>

        <div className="row">
          <div className="col-xs-12 col-sm-6 frontpage-box">
            <div className="list-header">
              <h2>Nyeste</h2>
            </div>
            <ul className="list-group">
              <li className="list-group-item"><em>Det er ingenting her</em></li>
            </ul>
          </div>
          <div className="col-xs-12 col-sm-6 frontpage-box frontpage-module">
            <div className="list-header">
              <h2 className="can-i-be-inline">Mest populære</h2>
              <div className="btn-group">
                <button className="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                    <span className="most-popular-label">
                      I dag
                    </span>
                  <span className="caret"/>
                </button>
                <ul className="dropdown-menu home-most-popular-dropdown">
                  <li>
                    <a data-delta="1" href="#">I dag</a>
                  </li>
                  <li className="disabled">
                    <a data-delta="2" href="#">Denne uka</a>
                  </li>
                  <li className="disabled">
                    <a data-delta="3" href="#">Denne måneden</a>
                  </li>
                  <li className="disabled">
                    <a data-delta="4" href="#">Dette året</a>
                  </li>
                  <li className="disabled">
                    <a data-delta="5" href="#">Alltid</a>
                  </li>
                </ul>
              </div>
            </div>
            <ul className="list-group">
              <li className="list-group-item"><em>Det er ingenting her</em></li>
            </ul>
          </div>
        </div>
        <div className="row">
          <div
            className="col-xs-12 col-sm-6 frontpage-box frontpage-module"
          >
            <div className="list-header">
              <h2 className="can-i-be-inline">Mest populære fag</h2>
              <div className="btn-group">
                <button className="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                    <span className="most-popular-label">
                      I dag
                    </span>
                  <span className="caret"/>
                </button>
                <ul className="dropdown-menu home-most-popular-dropdown">
                  <li>
                    <a data-delta="1" href="#">I dag</a>
                  </li>
                  <li className="disabled">
                    <a data-delta="2" href="#">Denne uka</a>
                  </li>
                  <li className="disabled">
                    <a data-delta="3" href="#">Denne måneden</a>
                  </li>
                  <li className="disabled">
                    <a data-delta="4" href="#">Dette året</a>
                  </li>
                  <li className="disabled">
                    <a data-delta="5" href="#">Alltid</a>
                  </li>
                </ul>
              </div>
            </div>
            <ul className="list-group">
              <li className="list-group-item"><em>Det er ingenting her</em></li>
            </ul>
          </div>
          <div className="col-xs-12 col-sm-6 frontpage-box">
            <div className="list-header">
              <h2>Siste besøkte fag</h2>
            </div>
            <ul className="list-group">
              <li className="list-group-item"><em>Det er ingenting her</em></li>
            </ul>
          </div>
        </div>
      </React.Fragment>
    );
  }
}

const mapStateToProps = ({ frontpage }) => ({
  started: frontpage.started,
  finished: frontpage.finished,

  number_files: frontpage.info.number_files,
  number_downloads: frontpage.info.number_downloads,
  number_courses_with_content: frontpage.info.number_courses_with_content,
  number_new_elements: frontpage.info.number_new_elements,
});

const mapDispatchToProps = {
};

export default connect(mapStateToProps, mapDispatchToProps)(InfoBoxesContainer);