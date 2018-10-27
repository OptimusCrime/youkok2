import React, { Component } from 'react';
import { connect } from 'react-redux';

import { BoxWrapper } from "../../../components/box-wrapper";
import { ElementItem } from "../../../components/element-item";
import { MostPopularDropdown } from "../../../components/most-popular/dropdown";
import { formatNumber } from "../../../../common/utils";
import { DELTA_POST_POPULAR_ELEMENTS } from "../../../consts";
import { updateFrontpage as updateFrontpageDispatch } from "../../../redux/frontpage/actions";

class BoxMostPopularElements extends Component {

  constructor(props) {
    super(props);

    this.state = {
      open: false
    };

    this.toggleDropdown = this.toggleDropdown.bind(this);
    this.changeDelta = this.changeDelta.bind(this);
  }

  toggleDropdown() {
    this.setState({
      open: !this.state.open
    });
  }

  changeDelta(delta) {
    const { updateFrontpage } = this.props;

    updateFrontpage(DELTA_POST_POPULAR_ELEMENTS, delta);
  }

  render() {

    const {
      isLoading,
      elementsMostPopular,
      userPreferences,
    } = this.props;

    const selectedButton = userPreferences[DELTA_POST_POPULAR_ELEMENTS];

    return (
      <BoxWrapper
        title="Mest populære fag"
        titleInline={true}
        isLoading={isLoading}
        isEmpty={!isLoading && elementsMostPopular.length === 0}
        dropdown={
          <MostPopularDropdown
            selectedButton={selectedButton}
            open={this.state.open}
            toggleDropdown={this.toggleDropdown}
            changeDelta={this.changeDelta}
          />
        }
      >
        {!isLoading && elementsMostPopular.map((element, index) =>
          <ElementItem element={element} key={index} additional={<span>[{formatNumber(element.downloads)}]</span>} /> )
        }
      </BoxWrapper>
    );
  }
}

const mapStateToProps = ({ frontpage }) => ({
  elementsMostPopular: frontpage.elements_most_popular,
  userPreferences: frontpage.user_preferences,
});

const mapDispatchToProps = {
  updateFrontpage: updateFrontpageDispatch
};

export default connect(mapStateToProps, mapDispatchToProps)(BoxMostPopularElements);