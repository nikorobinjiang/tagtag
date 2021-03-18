GET alllog/_search
{
    "size": 0,
    "aggs": {
        "my_buckets": {
            "composite": {
                "sources": [
                    {
                        "date": {
                            "date_histogram": {
                                "field": "add_time",
                                "interval": "1d",
                                "format": "yyyy-MM-dd"
                            }
                        }
                    },
                    {
                        "game_id": {
                            "terms": {
                                "field": "game_id.keyword"
                            }
                        }
                    },
                    {
                        "promote_id": {
                            "terms": {
                                "field": "promote_id.keyword"
                            }
                        }
                    },
                    {
                        "sub_promote_id": {
                            "terms": {
                                "field": "sub_promote_id.keyword"
                            }
                        }
                    }
                ]
            }
        }
    },
    "query": {
        "bool": {
            "must": [
                {
                    "match_all": {}
                },
                {
                    "match_phrase": {
                        "action": {
                            "query": "scat_game_promote"
                        }
                    }
                }
            ],
            "filter": [],
            "should": [],
            "must_not": []
        }
    }
}