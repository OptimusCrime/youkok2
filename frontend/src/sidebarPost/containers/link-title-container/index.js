import React from 'react';
import {connect} from "react-redux";
import {SIDEBAR_POST_LINK_CHANGE, SIDEBAR_POST_LINK_TITLE_CHANGE} from "../../redux/link/constants";
import {fetchTitleFromUrl as fetchTitleFromUrlDispatch} from "../../redux/link/actions";

const INPUT_HTML_ID = 'sidebar-create__link--title-input';

const LinkTitleContainer = props => {

  const {
    url,
    title,
    titleChange,
    fetchTitleFromUrl,
    title_empty,
    title_started,
    title_failed,
  } = props;

  return (
    <div className="form-group sidebar-create__link--title">
      <label htmlFor={INPUT_HTML_ID}>Tittel</label>
      <div className="sidebar-create__link--title--input-wrapper">
        <input
          type="text"
          className="form-control"
          placeholder="Øving 4, høsten 2014"
          value={title}
          onChange={e => titleChange(e.target.value)}
          id={INPUT_HTML_ID}
        />
        {title_started &&
        <div className="sidebar-create__link--title--loading">
          <i className="fa fa-spinner fa-spin"/>
        </div>
        }
      </div>
      <div className="sidebar-create__link--title--fetch">
        <a
          href="#"
          onClick={e => {
            e.preventDefault();

            fetchTitleFromUrl(url);
          }}
        >
          Forsøk å laste tittel direkte fra siden
        </a>
        {(title_empty || title_failed) &&
        <div className="alert alert-info sidebar-create__link--warning" role="alert">Klarte ikke å finne en tittel.</div>
        }
      </div>
    </div>
  );
};
const mapStateToProps = ({link}) => ({
  url: link.url,
  title: link.title,
  title_empty: link.title_empty,
  title_started: link.title_started,
  title_finished: link.title_finished,
  title_failed: link.title_failed,
});

const mapDispatchToProps = {
  titleChange: value => ({type: SIDEBAR_POST_LINK_TITLE_CHANGE, value}),
  fetchTitleFromUrl: fetchTitleFromUrlDispatch
};

export default connect(mapStateToProps, mapDispatchToProps)(LinkTitleContainer);
