import React, { Component } from 'react';
import { connect } from 'react-redux';

import { InfoBox } from '../components/info-box';
import {formatThousandNumber, formatNumber, loading } from '../../common/utils';

class InfoBoxesContainer extends Component {

  render() {

    const {
      failed,
      started,
      finished,

      number_files,
      number_downloads,
      number_courses_with_content,
      number_new_elements,
    } = this.props;

    const isLoading = loading(started, finished);
    const downloadsRemaining = 1_000_000 - number_downloads;

    return (
        <>
            <div className="row">
                <div className="col-xs-12 archive-title">
                    <div className="alert alert-success" role="alert">
                        <p>
                            Lyst til å prøve noe nytt? Vi har lansert{" "}
                            <a href="https://apps.apple.com/us/app/boost-me-boost-you/id6739365322" target="_blank">Boost Me - A social game experiment</a>{" "}til iPhone.
                        </p>
                        <p>
                            Last ned fra{" "}<a href="https://apps.apple.com/us/app/boost-me-boost-you/id6739365322" target="_blank">App Store</a>{" "}eller besøk{" "}<a href="https://boostmeapp.com" target="_blank">boostmeapp.com</a>{" "}for mer informasjon.
                        </p>
                    </div>
                </div>
            </div>
            <div className="row">
                    <div className="col-xs-12 archive-title">
                        <div className="alert alert-success" role="alert">
                            {downloadsRemaining < 0 ? (
                                <>
                                    Vi har bestemt oss for å legge ned Youkok2.com.
                                </>
                            ) : (
                                <>
                                    Youkok2.com legges ned om {formatNumber(1_000_000 - number_downloads)} nedlastninger.
                                </>
                            )}
                        </div>
                    </div>
            </div>
            <div className="row">
                <InfoBox
                    name="downloads"
                    icon="fa-download"
                    text="Nedlastninger"
                    number={number_downloads}
                    isLoading={isLoading}
                    failed={failed}
                    formatter={formatThousandNumber}
                />
                <InfoBox
                    name="last-month"
                    icon="fa-star"
                    text="Bidrag siste mnd"
                    number={number_new_elements}
                    isLoading={isLoading}
                    failed={failed}
                    formatter={formatNumber}
                />
                <InfoBox
                    name="files"
                    icon="fa-archive"
                    text="Filer og linker"
                    number={number_files}
                    isLoading={isLoading}
                    failed={failed}
                    formatter={formatNumber}
                />
                <InfoBox
                    name="courses"
                    icon="fa-graduation-cap"
                    text="Fag med innhold"
                    number={number_courses_with_content}
                    isLoading={isLoading}
                    failed={failed}
                    formatter={formatNumber}
                />
            </div>
        </>
    );
  }
}

const mapStateToProps = ({boxes}) => ({
    failed: boxes.failed,
    started: boxes.started,
    finished: boxes.finished,

    number_files: boxes.number_files,
    number_downloads: boxes.number_downloads,
    number_courses_with_content: boxes.number_courses_with_content,
    number_new_elements: boxes.number_new_elements,
});

const mapDispatchToProps = {};

export default connect(mapStateToProps, mapDispatchToProps)(InfoBoxesContainer);
