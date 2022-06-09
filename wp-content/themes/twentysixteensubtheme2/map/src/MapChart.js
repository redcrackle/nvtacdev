import React, {Component} from "react";
import { geoCentroid } from "d3-geo";
import {
  ComposableMap,
  Geographies,
  Geography,
  Marker,
  Annotation
} from "react-simple-maps";
import Select from 'react-select';

import allStates from "./data/allstates.json";

const geoUrl = "https://cdn.jsdelivr.net/npm/us-atlas@3/states-10m.json";

const offsets = {
  VT: [50, -8],
  NH: [34, 2],
  MA: [30, -1],
  RI: [28, 2],
  CT: [35, 10],
  NJ: [34, 1],
  DE: [33, 0],
  MD: [47, 10],
  DC: [49, 21]
};

class MapChart extends Component {

  constructor(props) {
    super(props);
    this.state = { grantees: '' };
    this.state = { countylist: '' };
    this.state = { ziplist: '' };
    this.state = { county: '', statevalue: '', zip: '' };
    this.handleClick = this.handleClick.bind(this);
    this.handleCountyChange = this.handleCountyChange.bind(this);
    this.handleZipChange = this.handleZipChange.bind(this);
    this.getGrantees = this.getGrantees.bind(this);
    this.getZipcodes = this.getZipcodes.bind(this);
    this.getGrantees();
    this.getZipcodes();
  }

  handleClick = async (geography) => {
    await this.setState({statevalue: geography.currentTarget.getAttribute("data-id"), county: '', zip: ''})
    this.getGrantees();
    this.getZipcodes();
  }

  handleCountyChange = async (selectedOption) => {
    await this.setState({county: selectedOption})
    this.getGrantees();
    this.getZipcodes();
  }

  handleZipChange = async (selectedOption) => {
    await this.setState({zip: selectedOption})
    this.getGrantees();
  }

  async getGrantees() {
    var state = this.state.statevalue;
    var county = this.state.county;
    var zip = this.state.zip;
    var county_val = '';
    if (county.value) {
      county_val = county.value;
    }

    var zip_val = '';
    if (zip.value) {
      zip_val = zip.value;
    }

    let response = await fetch(`http://nvtac.debugme.in/wp-json/grantee/v1/map?state=${state}&county=${county_val}&zip=${zip_val}`)
        .then(response => {
          return response.json();
        })
        .then((responseData) => {
          this.setState({ grantees: responseData });
          this.setState({ countylist: responseData });
        })
        .catch(error => {
          console.log(error);
        });

  }

  async getZipcodes() {
    var state = this.state.statevalue;
    var county = this.state.county;
    var zip = this.state.zip;
    var county_val = '';
    if (county.value) {
      county_val = county.value;
    }

    var zip_val = '';
    if (zip.value) {
      zip_val = zip.value;
    }

    let response_zip = await fetch(`http://nvtac.debugme.in/wp-json/grantee/v1/zip?state=${state}&county=${county_val}&zip=${zip_val}`)
        .then(response_zip => {
          return response_zip.json();
        })
        .then((responseData) => {
          this.setState({ ziplist: responseData });
        })
        .catch(error => {
          console.log(error);
        });

  }

  render () {
    var items = '';
    var countyitems = [];
    var zipitems = [];
    if (this.state.grantees) {
      const mapitems = JSON.parse(this.state.grantees);
      items = mapitems.map(function(row, i) {
        return <div className="tcontainer">
          <div className="container">
            <div className="item1"><h4>{row.grantee_name}</h4>
              <div className="description">
                <p>{row.grantee_first_name} {row.grantee_last_name}</p>
                <p><strong>Address: </strong>{row.street1 !=='' && `${row.street1},`} {row.street2 !=='' && `${row.street2},`} {row.city}, {row.state}, {row.zip}</p>
                <p>{row.grantee_phone_number}</p>
                <p><a href={`mailto:${row.grantee_email}`}>{row.grantee_email}</a></p>
                <p><strong>Serving:</strong> {row.service_delivery_area}</p>
              </div>
            </div>
          </div>
        </div>
      })
    }

    if (this.state.countylist) {
      const countylist = JSON.parse(this.state.countylist);
      const selectedcounty = this.state.county;
      countylist.map(function(row, i) {
        var counties = row.service_delivery_area.split(",");
        counties.map(function(items, y) {
          if (items) {
            countyitems.push({value: items.trim(), label: items.trim()})
          }
        })
      })
    }

    if (this.state.ziplist) {
      const ziplist = JSON.parse(this.state.ziplist);
      const selectedzip = this.state.zip;
      ziplist.map(function(row, i) {
        if (row.zip) {
          zipitems.push({value: row.zip, label: row.zip})
        }
      })
    }

    return (
        <div className="map-container entry-content">
        <ComposableMap projection="geoAlbersUsa">
          <Geographies geography={geoUrl}>
            {({geographies}) => (
                <>
                  {geographies.map(geo => {
                    const cur = allStates.find(s => s.val === geo.id);
                    var fillcolor = '#44a6da';
                    if (cur.id === this.state.statevalue) {
                      fillcolor = '#2f5083';
                    }
                    return (
                        <Geography
                            key={geo.rsmKey}
                            stroke="#FFF"
                            geography={geo}
                            fill={fillcolor}
                            onClick={this.handleClick}
                            data-id={cur.id}
                        />
                    )
                  })}
                  {geographies.map(geo => {
                    const centroid = geoCentroid(geo);
                    const cur = allStates.find(s => s.val === geo.id);
                    var fillclass='';
                    if (cur.id === 'HI') {
                      fillclass = 'transparenttext';
                    }
                    return (
                        <g key={geo.rsmKey + "-name"}>
                          {cur &&
                          centroid[0] > -160 &&
                          centroid[0] < -67 &&
                          (Object.keys(offsets).indexOf(cur.id) === -1 ? (
                              <Marker coordinates={centroid}>
                                <text y="2" fontSize={14} textAnchor="middle" className={fillclass}>
                                  {cur.id}
                                </text>
                              </Marker>
                          ) : (
                              <Annotation
                                  subject={centroid}
                                  dx={offsets[cur.id][0]}
                                  dy={offsets[cur.id][1]}
                              >
                                <text x={4} fontSize={14} alignmentBaseline="middle">
                                  {cur.id}
                                </text>
                              </Annotation>
                          ))}
                        </g>
                    );
                  })}
                </>
            )}
          </Geographies>
        </ComposableMap>
          <div className="container flex-justify">
            <div className="item2"><h5>Grantees </h5></div>
            <div className="item2">
              <div className="county_d">
                <Select
                    value={this.state.county}
                    onChange={this.handleCountyChange}
                    options={countyitems}
                />
              </div>
            </div>
            <div className="item2">
              <div className="zip_d">
                <Select
                    value={this.state.zip}
                    onChange={this.handleZipChange}
                    options={zipitems}
                />
              </div>
            </div>
          </div>
          <div className="container">
            <div className="item">Details</div>
          </div>
          {items}
          {items.length === 0 &&
          <div className="message">If the county you are looking for is not listed, please visit the <a
              href="https://www.careeronestop.org/LocalHelp/service-locator.aspx">Service Locator Local Help |
            CareerOneStop</a> website to find a local American Job Center that can assist you</div>
          }
        </div>
    );
  };
};

export default MapChart;
