/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh React component instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter, Route, Switch } from 'react-router-dom';
import '../css/app.css';

import User from './components/User';
import Login from './components/Login';

export default function Dawid() {
  return (
    <div className="wrapper">
      <h1>Hello</h1>
      <BrowserRouter>
        <Switch>
          <Route path="/" exact component={User} >
          </Route>
          <Route path="/login" exact>
            <Login />
          </Route>
        </Switch>
      </BrowserRouter>
    </div>
  );
}

ReactDOM.render(<Dawid />, document.getElementById('App'));
// // DOM element
// if (document.getElementById('App')) {
//     var React = require('react');
//     var ReactDOM = require('react-dom');
//     ReactDOM.render(<Dawid />, document.getElementById('App'));
// }
