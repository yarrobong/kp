const itemsBody = document.querySelector("#items-body");
const addRowBtn = document.querySelector("#add-row");
const totalNetEl = document.querySelector("#total-net");
const totalVatEl = document.querySelector("#total-vat");
const totalGrossEl = document.querySelector("#total-gross");
const vatInput = document.querySelector('[name="vat_rate"]');
const currencyInput = document.querySelector('[name="currency"]');

const money = (value, currency) =>
  `${value.toLocaleString("ru-RU", { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ${currency || ""}`.trim();

const parseNum = (val) => {
  const num = parseFloat(String(val).replace(",", ".")) || 0;
  return num < 0 ? 0 : num;
};

function updateIndexes() {
  itemsBody.querySelectorAll(".index").forEach((cell, idx) => {
    cell.textContent = idx + 1;
  });
}

function recalcTotals() {
  const currency = currencyInput?.value || "";
  const vatRate = parseNum(vatInput?.value);
  let net = 0;

  itemsBody.querySelectorAll("tr").forEach((row) => {
    const qty = parseNum(row.querySelector('[name="item_qty[]"]')?.value);
    const price = parseNum(row.querySelector('[name="item_price[]"]')?.value);
    const discount = parseNum(row.querySelector('[name="item_discount[]"]')?.value);
    const sum = qty * price * (1 - discount / 100);
    net += sum;
    const sumCell = row.querySelector(".sum");
    if (sumCell) sumCell.textContent = money(sum, currency);
  });

  const vatAmount = net * (vatRate / 100);
  const gross = net + vatAmount;
  totalNetEl.textContent = money(net, currency);
  totalVatEl.textContent = money(vatAmount, currency);
  totalGrossEl.textContent = money(gross, currency);
}

function addRow(data = {}) {
  const tr = document.createElement("tr");
  tr.innerHTML = `
    <td class="index"></td>
    <td><input name="item_name[]" required placeholder="Название" value="${data.name || ""}"></td>
    <td><input name="item_qty[]" type="number" step="0.01" value="${data.qty || 1}"></td>
    <td><input name="item_unit[]" value="${data.unit || "шт."}"></td>
    <td><input name="item_price[]" type="number" step="0.01" value="${data.price || 0}"></td>
    <td><input name="item_discount[]" type="number" step="0.1" value="${data.discount || 0}"></td>
    <td class="sum">0</td>
    <td><button type="button" class="icon-btn" data-remove>&times;</button></td>
  `;
  itemsBody.appendChild(tr);
  updateIndexes();
  recalcTotals();
}

function ensureAtLeastOneRow() {
  if (itemsBody.querySelectorAll("tr").length === 0) {
    addRow();
  }
}

addRowBtn?.addEventListener("click", () => addRow());

itemsBody?.addEventListener("click", (event) => {
  const btn = event.target.closest("[data-remove]");
  if (!btn) return;
  const row = btn.closest("tr");
  if (row) {
    row.remove();
    updateIndexes();
    recalcTotals();
    ensureAtLeastOneRow();
  }
});

itemsBody?.addEventListener("input", (event) => {
  if (event.target.matches("input")) recalcTotals();
});

vatInput?.addEventListener("input", recalcTotals);
currencyInput?.addEventListener("input", recalcTotals);

// Инициализация
updateIndexes();
recalcTotals();



