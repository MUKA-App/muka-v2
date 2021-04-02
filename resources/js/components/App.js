import React from 'react';
import ReactDOM from 'react-dom';

import '../../sass/app.scss';
import Header from './Header';
import Home from './Home';
import About from './About';
import Sponsors from './Sponsors';
import Login from './Login';
import Register from './Register';
import Footer from './Footer';

import {Redirect, BrowserRouter, Route, Switch} from 'react-router-dom';

export default function App() {

    return (
        <div className="App">
            <BrowserRouter>
                <Header />
                <Switch>
                    <Route exact path={'/register'}><Register /></Route>
                    <Route exact path={'/login'}><Login /></Route>
                    <Route exact path={'/sponsors'}><Sponsors /></Route>
                    <Route exact path={'/home'}><Home /></Route>
                    <Route exact path={'/about'}><About /></Route>
                    <Redirect to={"/home"}/>
                </Switch>
            </BrowserRouter>
            <Footer/>
        </div>
    )
}

// DOM element
if (document.getElementById('user')) {
    ReactDOM.render(<App/>, document.getElementById('user'));
}
