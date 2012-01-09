
describe 'simpletab', ->
  counter = 0
  el      = null
  tab     = null

  beforeEach ->
    counter++
    $('body').append """
      <div id="simpletab-eg-#{counter}">
        <div class='tabnav'>
          <ul>
            <li><a href="#simpletab-tab-#{counter}-0">Tab 0</a></li>
            <li><a href="#simpletab-tab-#{counter}-1">Tab 1</a></li>
          </ul>
        </div>
        <div id="simpletab-tab-#{counter}-0"><p>Content in Tab 0</p></div>
        <div id="simpletab-tab-#{counter}-1"><p>Content in Tab 1</p></div>
      </div>
    """
    el = $("#simpletab-eg-#{counter}")
    el.simpletab(
      nav_list: '.tabnav ul'
    )
    tab = el.data 'simpletab'

  afterEach ->
    el.remove()
    el  = null
    tab = null

  it 'should show only the first tab', ->
    expect($("#simpletab-tab-#{counter}-0").is(':visible')).toBeTruthy()
    expect($("#simpletab-tab-#{counter}-1").is(':visible')).toBeFalsy()

  it 'should show the second tab when you click the second list item', ->
    $("#simpletab-eg-#{counter} li:nth-child(2)").click()
    expect($("#simpletab-tab-#{counter}-0").is(':visible')).toBeFalsy()
    expect($("#simpletab-tab-#{counter}-1").is(':visible')).toBeTruthy()

  it 'should show the first tab when you click the second item, then the first item', ->
    $("#simpletab-eg-#{counter} li:nth-child(2)").click()
    $("#simpletab-eg-#{counter} li:first").click()
    expect($("#simpletab-tab-#{counter}-0").is(':visible')).toBeTruthy()
    expect($("#simpletab-tab-#{counter}-1").is(':visible')).toBeFalsy()

  it 'should fire a tabchange event when a tab is clicked on', ->
    clicked = 0
    el.bind('tabchange', (event) -> clicked++)
    $("#simpletab-eg-#{counter} li:nth-child(2)").click()
    $("#simpletab-eg-#{counter} li:first").click()
    expect(clicked).toBe(2)

