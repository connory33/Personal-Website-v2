import React from 'react';

const SearchForm = () => {
  return (
    <div className="flex justify-center mt-6">
      <form
        id="nhl-search"
        method="GET"
        action="nhl_games.php"
        className="bg-white backdrop-blur-sm px-4 sm:px-6 py-4 rounded-lg flex flex-col sm:flex-row gap-4 items-stretch sm:items-center w-full max-w-4xl shadow-md"
      >
        <select
          name="search_column"
          id="nhl-search-column"
          className="w-full sm:w-auto flex-1 bg-white text-black text-sm rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="season">Season</option>
          <option value="gameDate">Game Date</option>
          <option value="easternStartTime">Start Time</option>
          <option value="gameType">Game Type</option>
          <option value="team">Team</option>
          <option value="homeTeamId">Home Team</option>
          <option value="awayTeamId">Away Team</option>
          <option value="player">Player Name</option>
        </select>

        <input
          type="text"
          name="search_term"
          id="search-term"
          placeholder="Enter search term"
          required
          className="w-full sm:flex-2 text-black px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />

        <input
          type="submit"
          value="Search"
          className="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-md transition-colors duration-200 cursor-pointer"
        />
      </form>
    </div>
  );
};

export default SearchForm;
