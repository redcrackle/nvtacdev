import React, {Component} from "react";
import { geoCentroid } from "d3-geo";
import {
  ComposableMap,
  Geographies,
  Geography,
  Marker,
  Annotation
} from "react-simple-maps";

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
    this.state = { county: '', statevalue: '', zip: '' };
    this.handleClick = this.handleClick.bind(this);
    this.handleCountyChange = this.handleCountyChange.bind(this);
    this.handleZipChange = this.handleZipChange.bind(this);
    this.getNames = this.getNames.bind(this);
    this.getNames();
  }

  handleClick = async (geography) => {
    await this.setState({statevalue: geography.currentTarget.getAttribute("data-id"), county: '', zip: ''})
    this.getNames();
  }

  handleCountyChange = async (event) => {
    await this.setState({county: event.target.value})
    this.getNames();
  }

  handleZipChange = async (event) => {
    await this.setState({zip: event.target.value})
    this.getNames();
  }

  async getNames() {
    var state = this.state.statevalue;
    var county = this.state.county;
    var zip = this.state.zip;

    let response = await fetch(`https://nvtac.org/wp-json/grantee/v1/map?state=${state}&county=${county}&zip=${zip}`)
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

  render () {
    var items = '';
    var countyitems = '';
    var zipitems = '';
    if (this.state.grantees) {
      const mapitems = JSON.parse(this.state.grantees);
      items = mapitems.map(function(row, i) {
        return <div className="tcontainer">
          <div className="container">
            <div className="item1"><h4>{row.grantee_name}</h4>
              <div className="description">
                <p>{row.grantee_first_name} {row.grantee_last_name}</p>
                <p>{row.grantee_phone_number}</p>
                <p><a href={`mailto:${row.grantee_email}`}>{row.grantee_email}</a></p>
                <p><strong>Serving:</strong> {row.service_delivery_area}</p>
              </div>
            </div>
          </div>
        </div>
      })

      zipitems = mapitems.map(function(row, i) {
        if (row.zip) {
          const zipcode = row.zip.split('-');
          return <option className="tcontainer">{zipcode[0]}</option>
        }
      })
    }

    if (this.state.countylist) {
      const countylist = JSON.parse(this.state.countylist);
      countyitems = countylist.map(function(row, i) {
        if (row.county) {
          return <option className="tcontainer">{row.county}</option>
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
                    return (
                        <Geography
                            key={geo.rsmKey}
                            stroke="#FFF"
                            geography={geo}
                            fill="#44a6da"
                            onClick={this.handleClick}
                            data-id={cur.id}
                        />
                    )
                  })}
                  {geographies.map(geo => {
                    const centroid = geoCentroid(geo);
                    const cur = allStates.find(s => s.val === geo.id);
                    return (
                        <g key={geo.rsmKey + "-name"}>
                          {cur &&
                          centroid[0] > -160 &&
                          centroid[0] < -67 &&
                          (Object.keys(offsets).indexOf(cur.id) === -1 ? (
                              <Marker coordinates={centroid}>
                                <text y="2" fontSize={14} textAnchor="middle">
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
            <div className="item2"><h5>State: California </h5></div>
            <div className="item2">
              <div className="county_d">
                <select className="event-type-select" name="county" onChange={this.handleCountyChange}>
                  <option value="all">Select County</option>
                  {countyitems}
                </select>
              </div>
            </div>
            <div className="item2">
              <div className="zip_d">
                <select className="event-type-zip" name="zip" onChange={this.handleZipChange}>
                  <option value="all">Select Zip code</option>
                  {zipitems}
                </select>
              </div>
            </div>
          </div>
          <div className="container">
            <div className="item">Grantee Name</div>
          </div>
        {items}
        </div>
    );
  };
};

export default MapChart;
