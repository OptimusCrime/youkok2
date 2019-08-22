import React from 'react';
import {
  LINK_POST_ERROR_BACKEND,
  LINK_POST_ERROR_TITLE,
  LINK_POST_ERROR_URL,
  TYPE_LINK,
  TYPE_NONE
} from "../../constants";
import {changeOpen as changeOpenDispatch} from "../../redux/main/actions";
import {postLink as postLinkDispatch} from "../../redux/link/actions";
import {connect} from "react-redux";
import {
  SIDEBAR_POST_LINK_CHANGE,
  SIDEBAR_POST_LINK_RESET,
  SIDEBAR_POST_LINK_TITLE_POST_ERROR
} from "../../redux/link/constants";
import LinkTitleContainer from "../link-title-container";

// Mirrored from backend
const MIN_VALID_URL_LENGTH = 4;
const MIN_VALID_TITLE_LENGTH = 2;
const INPUT_HTML_ID = 'sidebar-create__link--url-input';

const LinkPostErrorMessage = ({type}) => {
  switch (type) {
    case LINK_POST_ERROR_URL:
      return (
        <div className="alert alert-danger sidebar-create__link--warning" role="alert">
          URLen er ikke gyldig.
        </div>
      );
    case LINK_POST_ERROR_TITLE:
      return (
        <div className="alert alert-danger sidebar-create__link--warning" role="alert">
          Du må gi bidraget ditt en tittel på minst to tegn.
        </div>
      );
    case LINK_POST_ERROR_BACKEND:
    default:
      return (
        <div className="alert alert-danger sidebar-create__link--warning" role="alert">
          Kunne ikke lagre ditt bidrag. Beklager. Prøv igjen eller rapporter problemet til oss.
        </div>
      );
  }
};

const LinkContainer = props => {
  const {
    reset,
    changeOpen,
    linkChange,
    linkPostError,
    postLink,
    url,
    valid,
    error,
    title,
    post_started,
    post_finished,
  } = props;

  return (
    <div className="sidebar-create__inner sidebar-create__link">
      <div className="form-group">
        <label htmlFor={INPUT_HTML_ID}>URL</label>
        <input
          type="text"
          className="form-control"
          placeholder="https://www.vg.no"
          value={url}
          onChange={e => linkChange(e.target.value)}
          id={INPUT_HTML_ID}
        />
        {url.length >= MIN_VALID_URL_LENGTH &&
        <React.Fragment>
          {valid
            ? <LinkTitleContainer/>
            :
            <div className="alert alert-danger sidebar-create__link--warning" role="alert">Ugyldig URL. Prøv igjen eller
              rapporter et problem.</div>
          }
        </React.Fragment>
        }
      </div>
      <div className="sidebar-create-submit">
        <button
          type="button"
          className="btn btn-default"
          onClick={() => {
            if (!post_started) {
              if (valid && title.length >= MIN_VALID_TITLE_LENGTH) {
                postLink(url, title);
              } else {
                if (!valid) {
                  linkPostError(LINK_POST_ERROR_URL);
                } else {
                  linkPostError(LINK_POST_ERROR_TITLE);
                }
              }
            }
          }}
          disabled={post_started}
        >
          {post_started
            ? 'Vent litt'
            : 'Post link'
          }
        </button>
        &nbsp;
        eller
        &nbsp;
        <a href="#" onClick={e => {
          e.preventDefault();

          reset();
          changeOpen(TYPE_NONE, TYPE_LINK);
        }}>avbryt</a>.
      </div>
      {error && <LinkPostErrorMessage type={error}/>}
      {post_finished &&
      <div className="alert alert-success sidebar-create__link--warning" role="alert">
        Takk for ditt bidrag. Bidraget vil bli behandlet og blir synlig på nettside når den er godkjent.
      </div>
      }
    </div>
  );
};

const mapStateToProps = ({link}) => ({
  url: link.url,
  valid: link.valid,
  title: link.title,
  error: link.error,
  post_started: link.post_started,
  post_finished: link.post_finished,
});

const mapDispatchToProps = {
  reset: () => ({type: SIDEBAR_POST_LINK_RESET }),
  linkChange: value => ({type: SIDEBAR_POST_LINK_CHANGE, value}),
  linkPostError: reason => ({type: SIDEBAR_POST_LINK_TITLE_POST_ERROR, reason}),
  changeOpen: changeOpenDispatch,
  postLink: postLinkDispatch,
};

export default connect(mapStateToProps, mapDispatchToProps)(LinkContainer);
