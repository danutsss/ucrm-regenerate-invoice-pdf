const inputFrom = document.getElementById("frm-from");
const inputTo = document.getElementById("frm-to");
const checkboxes = document.querySelectorAll('input[type="checkbox"]');

inputTo.addEventListener("change", () => {
	const from = parseInt(inputFrom.value);
	const to = parseInt(inputTo.value);

	checkboxes.forEach((checkbox, i) => {
		if (i >= from && i <= to) {
			checkboxes[i].checked = true;
		} else {
			checkboxes[i].checked = false;
		}

		if (from > to) {
			checkboxes[i].checked = false;
		}

		if (from == 0 && to == 0) {
			checkboxes[i].checked = false;
		}

		if (from == 0 && to == checkboxes.length) {
			checkboxes[i].checked = true;
		}
	});
});
