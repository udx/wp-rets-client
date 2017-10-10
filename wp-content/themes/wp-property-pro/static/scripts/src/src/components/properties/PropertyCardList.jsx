import React, {Component} from 'react';
import { findDOMNode } from 'react-dom';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import difference from 'lodash/difference';
import get from 'lodash/get';
import find from 'lodash/find';

import PropertyCard from 'app_root/components/PropertyCard.jsx';
import Api from 'app_root/containers/Api.jsx';
import {
  receivePropertySingleResult,
  receivePropertySingleFetchingError,
  requestPropertySingleResult,
  selectPropertyOnMap,
  deselectPropertyOnMap,
} from 'app_root/actions/index.jsx';

class PropertyCardList extends Component {
  static propTypes = {
    properties: PropTypes.array.isRequired,
    selectedProperty: PropTypes.string
  }

  constructor(props) {
    super(props);
    this.state = {};
    this.propertiesDOM = {};
  }

  componentDidMount() {
    if (this.props.properties.length && this.props.selectedProperty) {
      this.scrollToProperty(this.props.selectedProperty)
    }
  }

  componentDidUpdate() {
    if (this.props.properties.length && this.props.selectedProperty) {
      this.scrollToProperty(this.props.selectedProperty)
    }
  }

  shouldComponentUpdate(nextProps) {
    return difference(nextProps.properties, this.props.properties).length ||
      nextProps.selectedProperty !== this.props.selectedProperty;
  }

  scrollToProperty(propertyId) {
    if (!this.propertiesDOM[propertyId]) {
     console.log('chosen property was not found in the results');
    } else {
      let node = findDOMNode(this.propertiesDOM[propertyId]);
      node.scrollIntoView({ behaviour: 'smooth' });
    }
  }

  handlePropertyClick = (propertyId) => {
    this.props.onUpdateSelectedProperty(propertyId);
<<<<<<< HEAD

    const property = find(this.props.properties, { '_id': propertyId });

    this.props.selectPropertyOnMap(property._source);
=======
    const property = this.getPropertyRecordByMlsID(propertyId);
    
    if (!property) {
      console.log('property was not found')
    } else {
      this.props.deselectPropertyOnMap();

      setTimeout(() => {
        this.props.selectPropertyOnMap(property._source);
      });
    }
>>>>>>> 07db5c3... better opening property panel on map
  }

  render() {
    let {
      selectedProperty,
      properties
    } = this.props;
    return (
      <div className="row">
        {
          properties.map((p, i) => {
            let item = {
              address: get(p, '_source.post_meta.rets_address', [''])[0],
              address_unit: get(p, '_source.post_meta.address_unit', '')[0],
              location: get(p, '_source.post_meta.wpp_location_pin', []),
              baths: get(p, '_source.post_meta.rets_total_baths', 0),
              beds: get(p, '_source.post_meta.rets_beds', 0),
              city: get(p, '_source.tax_input.wpp_location.wpp_location_city[0].name', ''),
              gallery_images: get(p, '_source.wpp_media', []).map((media) => media.url),
              id: p._id,
              living_area: get(p, '_source.post_meta.rets_living_area', 0),
              lots_size: get(p, '_source.post_meta.rets_lot_size_area', 0),
              price: get(p, '_source.post_meta.rets_list_price[0]', 0),
              post_name: get(p, '_source.post_name', 0),
              state: get(p, '_source.tax_input.wpp_location.wpp_location_state[0].name', ''),
              type: get(p, '_source.tax_input.wpp_listing_type.listing_type[0].slug', ''),
              sqft: get(p, '_source.post_meta.sqft[0]', ''),
              sub_type: get(p, '_source.tax_input.wpp_listing_type.listing_sub_type[0].name', ''),
              relative_permalink: get(p, '_source.permalink', ''),
              thumbnail: get(p, '_source.post_meta.rets_thumbnail_url', [''])[0],
              zip: get(p, '_source.post_meta.rets_postal_code[0]', '')
            };
            return (
              <div className={`col-12 col-lg-6`} key={p._id}>
                <PropertyCard
                  data={item}
                  highlighted={selectedProperty === p._id}
                  propertiesDOM={this.propertiesDOM}
                  openPanelWhenClicked={true}
                  onClickCard={this.handlePropertyClick}
                />
              </div>
            );
          })
        }
      </div>
    );
  }
}

const mapStateToProps = (state) => {
  return {

  };
};

const mapDispatchToProps = (dispatch) => {
  return {
    deselectPropertyOnMap: () => {
      dispatch(deselectPropertyOnMap());
    },
    selectPropertyOnMap: (property) => {
      dispatch(selectPropertyOnMap(property));
    }
  }
}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(PropertyCardList);
