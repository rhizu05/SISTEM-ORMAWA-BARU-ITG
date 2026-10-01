<style>
/* Wrapper style for Opsi 3 */
.skin-calendar-wrapper {
    background: linear-gradient(180deg, #FFFFFF 0%, #F1F5F9 100%);
    border-radius: 1rem;
    box-shadow: 0 12px 40px rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(226, 232, 240, 0.8);
    overflow: hidden;
}

/* Header Toolbar */
.skin-calendar-wrapper .fc-header-toolbar {
    background-color: #0B1528 !important;
    color: #F8FAFC !important;
    padding: 1.25rem 1.5rem !important;
    margin-bottom: 0 !important;
}

/* Header Title */
.skin-calendar-wrapper .fc-toolbar-title {
    font-size: 1.125rem !important;
    font-weight: 700 !important;
    font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
}

/* Buttons */
.skin-calendar-wrapper .fc-button-primary {
    background-color: rgba(255, 255, 255, 0.1) !important;
    border: none !important;
    text-transform: capitalize !important;
    font-weight: 500 !important;
    border-radius: 0.5rem !important;
    box-shadow: none !important;
    color: #F8FAFC !important;
}
.skin-calendar-wrapper .fc-button-primary:hover {
    background-color: rgba(255, 255, 255, 0.2) !important;
}
.skin-calendar-wrapper .fc-button-primary:disabled {
    opacity: 0.5 !important;
}
.skin-calendar-wrapper .fc-button-active {
    background-color: #FFFFFF !important;
    color: #0B1528 !important;
    font-weight: 700 !important;
}

/* Day Headers */
.skin-calendar-wrapper .fc-theme-standard th {
    border: none;
    border-bottom: 1px solid #E2E8F0;
    background-color: #F8FAFC;
    padding: 0.5rem 0;
}
.skin-calendar-wrapper .fc-col-header-cell-cushion {
    color: #6366F1;
    font-weight: 700;
    font-size: 0.875rem;
    text-decoration: none !important;
}
/* Sunday header red */
.skin-calendar-wrapper .fc-day-sun .fc-col-header-cell-cushion {
    color: #EF4444;
}

/* Grid & Cells */
.skin-calendar-wrapper .fc-theme-standard td {
    border-color: #F1F5F9;
}
.skin-calendar-wrapper .fc-daygrid-day-frame {
    padding: 4px;
}
.skin-calendar-wrapper .fc-daygrid-day-number {
    font-size: 0.75rem;
    color: #334155;
    font-weight: 500;
    text-decoration: none !important;
    padding: 0.25rem 0.5rem !important;
}
.skin-calendar-wrapper .fc-day-sun .fc-daygrid-day-number {
    color: #EF4444;
}

/* Today highlight */
.skin-calendar-wrapper .fc-day-today {
    background-color: #EEF2FF !important;
}
.skin-calendar-wrapper .fc-day-today .fc-daygrid-day-number {
    background-color: #6366F1;
    color: #FFFFFF !important;
    border-radius: 9999px;
    width: 24px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 4px;
    padding: 0 !important;
}

/* Events */
.skin-calendar-wrapper .fc-event {
    border: none !important;
    border-radius: 6px !important;
    padding: 3px 6px !important;
    font-size: 0.7rem !important;
    font-weight: 600 !important;
    margin-bottom: 3px !important;
}
.skin-calendar-wrapper .fc-event-title {
    font-weight: 600 !important;
}
.skin-calendar-wrapper .fc-timegrid-event {
    border-radius: 6px !important;
}
</style>
