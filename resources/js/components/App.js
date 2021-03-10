import React from 'react';
import ReactDOM from 'react-dom';

function App() {
    return (
        <div>
            
        </div>
        // <div className="container mt-5">
        //     <div className="row justify-content-center">
        //         <div className="col-md-8">
        //             <div className="card text-center">
        //                 <div className="card-header"><h2>Hell</h2></div>
        //
        //                 <div className="card-body">I'm tiny React component in Laravel app!</div>
        //             </div>
        //         </div>
        //     </div>
        // </div>
    );
}

// DOM element
if (document.getElementById('user')) {
    ReactDOM.render(<App />, document.getElementById('user'));
}