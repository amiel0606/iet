import ExcelJS from "exceljs";
import axios from "axios";

export const bulkImport = async (file) => {
    if (!file) return alert("No file selected.");

    const reader = new FileReader();
    reader.readAsArrayBuffer(file);

    reader.onload = async () => {
        const workbook = new ExcelJS.Workbook();
        await workbook.xlsx.load(reader.result);
        const worksheet = workbook.worksheets[0];

        const title = worksheet.getCell("A1").value;
        let type =
            title === "Income Tracker"
                ? "income"
                : title === "Expense Tracker"
                ? "expense"
                : "";

        if (!type) {
            alert(
                "Invalid file. A1 should be 'Income Tracker' or 'Expense Tracker'."
            );
            return;
        }

        let data = [];
        worksheet.eachRow((row, rowNumber) => {
            if (rowNumber >= 3) {
                const rowData = {
                    date: row.getCell(1).value,
                    amount: row.getCell(2).value,
                    category: row.getCell(3).value,
                    description: row.getCell(4).value,
                };
                if (rowData.date && rowData.amount) data.push(rowData);
            }
        });

        if (data.length === 0) {
            alert("No valid data found.");
            return;
        }

        try {
            const response = await axios.post(`/${type}/bulk-import`, { data });
            alert(response.data.message);
            location.reload();
        } catch (error) {
            console.error(
                "Import error:",
                error.response?.data || error.message
            ); 
            alert(
                "Import failed: " +
                    (error.response?.data?.error || "Unknown error")
            );
            location.reload();
        }
    };
};
