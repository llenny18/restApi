const base64 = require('base-64');

// Encoding
const jsonString = JSON.stringify({ name: "John", age: 30 });
const encoded = base64.encode(jsonString);
console.log("Encoded:", encoded);

// Decoding
const decoded = base64.decode(encoded);
console.log("Decoded:", decoded);