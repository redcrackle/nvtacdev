const { render } = wp.element; //we are using wp.element here!
import App from './App';
if (document.getElementById('grantee-map')) { //check if element exists before rendering
  render(<App />, document.getElementById('grantee-map'));
}