const fetchData = async () => {
    try {
        // Make a request to the PHP script that returns student data as JSON
        const response = await fetch('fetch_data.php'); // Adjust URL if necessary
        const data = await response.json();

        // Check if there is an error in the data response
        if (data.error) {
            console.error('Error:', data.error);
        } else {
            // Process and display the data dynamically
            let studentDataProcessed = '';
            data.forEach((item, index) => {
                studentDataProcessed += `Attempt ${index + 1}:\n`;
                studentDataProcessed += `Financial Accounting: ${item['Financial Accounting and Reporting']}\n`;
                studentDataProcessed += `Advanced Financial Accounting: ${item['Advanced Financial Accounting and Reporting']}\n`;
                studentDataProcessed += `Management Services: ${item['Management Services']}\n`;
                studentDataProcessed += `Auditing: ${item['Auditing']}\n`;
                studentDataProcessed += `Taxation: ${item['Taxation']}\n`;
                studentDataProcessed += `Regulatory Framework: ${item['Regulatory Framework for Business Transaction']}\n`;
                studentDataProcessed += '--------------------------\n';
            });

            // Display the processed data on the page (or update the page dynamically)
            document.getElementById("studentDataDisplay").innerText = studentDataProcessed;
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    }
};

// Call the fetchData function
fetchData();
