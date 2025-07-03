import React, { Component } from 'react';
import MapChart from "./MapChart";

class App extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div className="App">
                <MapChart />
            </div>
        );
    }
}

export default App;