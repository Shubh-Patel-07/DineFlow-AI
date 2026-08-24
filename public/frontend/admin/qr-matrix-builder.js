/**
 * DineFlow AI - Table QR Code Matrix & SVG Generator Helper
 *
 * @author Fiyans Patel <patelfiyans315@gmail.com>
 * @version 1.0.0
 */
class QrMatrixBuilder {
  static buildTableUrl(restaurantId, tableId, origin = window.location.origin) {
    return `${origin}/customer/menu.html?restaurantId=${encodeURIComponent(restaurantId)}&tableId=${encodeURIComponent(tableId)}`;
  }

  static generatePrintPayload(table, restaurantInfo) {
    return {
      table_number: table.table_number || `T-${table.id}`,
      capacity: table.capacity || 4,
      section: table.section || 'Main Hall',
      restaurant_name: restaurantInfo.name || 'DineFlow Gourmet',
      qr_url: this.buildTableUrl(table.restaurant_id || 1, table.id),
      created_at: new Date().toISOString()
    };
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = QrMatrixBuilder;
}
